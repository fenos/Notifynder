<?php namespace Fenos\Notifynder\Handler;

use Fenos\Notifynder\Notifynder;
use Illuminate\Foundation\Application;

/**
* 
*/
class NotifynderHandler
{
	/**
	* @var instance of Illuminate\Foundation\Application
	*/
	public $app;

	/**
	* @var listener notifications
	*/
	protected $listener = [];

	function __construct(Application $app)
	{
		$this->app = $app;
	}
	
	/**
	* Load all the listener availables
	*
	* @param $listen 	(Array)
	*/
	public function listen(array $listen)
	{
		return $this->listener[$listen['key']] = $listen['handler'];
	}

	/**
	* Fire the method with the logic of your notification
	* It will execute the closure just if the method fired
	* Will not return false
	*
	* @param $key 		(String)
	* @param $values 	(Array)
	* @return Closure \ False
	*/
	public function fire(Notifynder $notifynder, $key, array $values)
	{
		// get the class and method of the current event 
		$method = $this->listener[$key];

		// get array of Class and method
		$ClassAndMethod = $this->getFunction($method);

		//instance of the class
		$class = $this->app->make(''.$ClassAndMethod[0].'');


		// there are any extra parameters?
		if (array_key_exists('values', $values)) // yes
		{
			$method = $class->$ClassAndMethod[1]($values['values']);
		}
		else 									// no
		{
			$method = $class->$ClassAndMethod[1]();
		}

		// if the method doesn't return false itialize the closure
		if ($method !== false)
		{
			if ($values['use'] instanceof \Closure )
			{
				return $values['use']($notifynder,$method);
			}
			else
			{
				throw new \InvalidArgumentException("The [use] value must be a Closure");
			}
		}

		return false;

	}

	/**
	* Return an array with as first value
	* The class and second the method
	*
	* @return Array()
	*/
	public function getFunction($stringFunction)
	{
		return explode('@', $stringFunction);
	}

	public function listListener()
	{
		return $this->listener;
	}
	
}