<?php namespace Fenos\Notifynder\Models\Collection;

use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;
use Fenos\Notifynder\Parse\NotifynderParse;
use Fenos\Notifynder\Translator\NotifynderTranslator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class NotifynderCollection
 *
 * @package Fenos\Notifynder\Models\Collection
 */
class NotifynderCollection extends Collection {

    /**
     * @var NotifynderTranslator
     */
    protected $notifynderTranslator;

    /**
     * @var NotifynderParse
     */
    protected $notifynderParse;

    /**
     * @param array                $models
     * @param NotifynderTranslator $notifynderTranslator
     */
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
     * @param $language (String)
     * @return Collection
     */
    public function translateCategory( $language )
    {
        foreach ($this->items as $key => $item)
        {
            try
            {
                $translation = $this->notifynderTranslator->translate( $language,$this->items[$key]['name'] );

                $this->items[$key]['text'] = $translation;
            }
            catch(NotificationTranslationNotFoundException $e)
            {
                $this->items[$key]['text'];
            }
        }

        return $this;
    }

    /**
     * This method of the collection will need it  for translate
     * the body text from when the category is in a nested query
     *
     * @param $language (String)
     * @return Collection
     */
    public function translateFromNotifications( $language )
    {
        foreach ($this->items as $key => $item)
        {
            try
            {
                $translation = $this->notifynderTranslator->translate( $language,$this->items[$key]['body']['name'] );

                $this->items[$key]['body']['text'] = $translation;
            }
            catch(NotificationTranslationNotFoundException $e)
            {
                $this->items[$key]['body']['text'];
            }
        }

        return $this;
    }
} 