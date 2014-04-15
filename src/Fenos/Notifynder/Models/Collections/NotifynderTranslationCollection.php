<?php namespace Fenos\Notifynder\Models\Collections;

use Illuminate\Database\Eloquent\Collection;
use Fenos\Notifynder\Translator\NotifynderTranslator;

//Exceptions
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;

class NotifynderTranslationCollection extends Collection
{
	/**
	* @var instance of Fenos\Notifynder\Translator\NotifynderTraslator
	*/
	protected $notifynderTranslator;

	function __construct($models,NotifynderTranslator $notifynderTranslator)
	{
		parent::__construct($models);
		$this->notifynderTranslator = $notifynderTranslator;

	}

	/**
	* Main method that translate both of the models
	* 
	* @param $language (String)
	* @return Collection
	*/
	public function translate( $language )
	{
		if ( !is_null($this->items[0]['body'] ) )
		{
			$this->translateFromNotifications( $language );
		} 
		else
		{
			$this->translateCategory( $language );
		}

		return $this;
	}

	/**
	* This method translate the body text from 
	* another language. It used by collection of
	* NotificationCategory (Eloquent)
	*
	*/
	public function translateCategory( $language )
	{
		foreach ($this->items as $key => $item) {

			try
			{
				$translation = $this->notifynderTranslator->translate( $language,$this->items[$key]['name'] );

				$this->items[$key]['text'] = $translation;
			}
			catch(NotificationTranslationNotFoundException $e)
			{
				$this->items[$key]['text'] = $this->items[$key]['text'];
			}
			
			
		}
		
		return $this;
	}

	/**
	* This method of the collection will need it  for translate 
	* the body text from when the category is in a nested query 
	*
	*/
	public function translateFromNotifications( $language )
	{
		foreach ($this->items as $key => $item) {

			try
			{
				$translation = $this->notifynderTranslator->translate( $language,$this->items[$key]['body']['name'] );

				$this->items[$key]['body']['text'] = $translation;
			}
			catch(NotificationTranslationNotFoundException $e)
			{
				$this->items[$key]['body']['text'] = $this->items[$key]['body']['text'];
			}
			
			
		}
		
		return $this;
	}

}