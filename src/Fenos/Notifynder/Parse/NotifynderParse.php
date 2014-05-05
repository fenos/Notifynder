<?php namespace Fenos\Notifynder\Parse;

use Fenos\Notifynder\Models\NotificationCategory;


/**
* Class parse used on collection. It permit to decode the special
* values inserted on the notification
*/
class NotifynderParse
{

	const RULE = '/\{(.+?)(?:\{(.+)\})?\}/';

	/**
	* @var instance of Fenos\Notifynder\Models\Notification
	*/
	protected $notification;

	/**
	* @var items container
	*/
	protected $items;

	/**
	* @var container lazy loading 
	*/
	protected $loader = array();
	
	function __construct( $items )
	{
		$this->items = $items;
	}

	/**
	* Parse the body of the notifications
	* Replacing the default value with the
	* right parameter
	*
	* @param $items 	(Obj)
	* @return  Collection
	*/
	public function parse()
	{
		// for each items of the collection
		foreach ($this->items as $key => $notification) {

			// get specials parameters between curly brachet
			$values = $this->getValues($this->items[$key]['body']['text']);

			// there are any?
			if ( count($values) == 0 ) // no
			{
				$this->items[$key]['body']['text'];
			}
			else // yes
			{
				$this->replaceSpecialValues($values,$key);
			}
		}

		return $this->items;
	}

	/**
	* Replace Special value of the body 
	* of the items I pass as secoond parameter
	* The key of the main array of the result so it can
	* merge the result properly
	*
	* @param $values (Array)
	* @return Collection
	*/
	public function replaceSpecialValues($values,$keyItems)
	{
		// for each special values
		foreach ($values as $value) {

			// get an array of nested values, means that there is a relations 
			// in progress
			$value_user = explode('.', $value);

			// get name relations
			$relation = array_shift($value_user);

			// check if there is any value with the name of the relation just in case
			if ( strpos($value, $relation.'.') !== false) // yes
			{
				// for each values with relations
				foreach ($value_user as $key => $value) {

					if (!array_key_exists($this->items[$keyItems]['body']['name'].$relation.$value, $this->loader))
					{

						// switch the special attribute with the right value
						$this->items[$keyItems]['body']['text'] = preg_replace("{{".$relation.".".$value."}}",$this->items[$keyItems][$relation][$value],$this->items[$keyItems]['body']['text']);
						
						// eager loading
						$this->loader[$this->items[$keyItems]['body']['name'].$relation.$value] = $this->items[$keyItems]['body']['text'];
					}

					$this->items[$keyItems]['body']['text'] = $this->loader[$this->items[$keyItems]['body']['name'].$relation.$value];
				}
			}
			else
			{
				// no values relations 
				$this->items[$keyItems]['body']['text'] = preg_replace("{{".$value."}}",$this->items[$keyItems]['extra'],$this->items[$keyItems]['body']['text']);
				
			}
		}

		return $this->items;
	}

	/**
	* Get the values between {}
	* and return an array of it
	*
	* @param $body 		(String)
	* @return $values 	(Array)
	*/
	public function getValues($body)
	{
		$values = [];
		preg_match_all(self::RULE, $body, $values);
		return $values[1];
	}
}