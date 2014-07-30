# Kohana Paginatable ORM

Paginatable ORM simplifies pagination. It allows you to do the follwoing.


```php
// In your controller
class aController {

	fucntion action_index()
    {
    	$users = ORM::factory('User')->where('active','=',1)->paginate(30);
    	$this->template->content = View::factory('users', array('users'=>$users));
    }

}

// In your view

echo $users->links(); // prints out pagination links

```

