<?php

class ORM_Paginatable extends Kohana_ORM
{
	protected $_paginate = null;
	protected $_paginate_config = null;

	public function paginate($itemsPerPage = 30, $config = [])
	{
		if ($this->_loaded)
			throw new Kohana_Exception('Method find_all() cannot be called on loaded objects');

		if ( ! empty($this->_load_with))
		{
			foreach ($this->_load_with as $alias)
			{
				// Bind auto relationships
				$this->with($alias);
			}
		}

		$this->_build(Database::SELECT);

		$this->_paginate = true;
		$this->_paginate_config = [$itemsPerPage, $config];

		return $this->_load_with_paginator(TRUE);
	}

	/**
	 * Loads a database result, either as a new record for this model, or as
	 * an iterator for multiple rows.
	 *
	 * @chainable
	 * @param  bool $multiple Return an iterator or load a single row
	 * @return ORM|Database_Result
	 */
	protected function _load_with_paginator($multiple = FALSE)
	{
		$this->_db_builder->from(array($this->_table_name, $this->_object_name));

		// Select all columns by default
		$this->_db_builder->select_array($this->_build_select());

		if ( ! isset($this->_db_applied['order_by']) AND ! empty($this->_sorting))
		{
			foreach ($this->_sorting as $column => $direction)
			{
				if (strpos($column, '.') === FALSE)
				{
					// Sorting column for use in JOINs
					$column = $this->_object_name.'.'.$column;
				}

				$this->_db_builder->order_by($column, $direction);
			}
		}


		$counter = clone $this;

		$default = array(
			'total_items' => $counter->count_all(),
			'items_per_page' => $this->_paginate_config[0]
		);

		$paginator = Pagination::factory(Arr::merge($default, $this->_paginate_config[1]));

		$this->_db_builder->limit($paginator->items_per_page);
		$this->_db_builder->offset($paginator->offset);

		$result = $this->_db_builder->as_object(get_class($this))->execute($this->_db);
		$result->paginator($paginator);

		$this->reset();

		return $result;
	}
}