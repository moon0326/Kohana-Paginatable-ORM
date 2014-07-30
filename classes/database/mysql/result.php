<?php

class Database_MySQL_Result extends Kohana_Database_MySQL_Result
{
	protected $_paginator = null;

	public function paginator(Pagination $paginator)
	{
		$this->_paginator = $paginator;
	}

	public function links($group = null)
	{
		 return $this->_paginator->render($group);
	}
}
