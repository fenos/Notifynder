<?php

namespace Fenos\Notifynder\Models;

use Fenos\Notifynder\Contracts\NotifynderTranslator;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;
use Fenos\Notifynder\Parsers\NotifynderParser;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class NotifynderCollection.
 */
class NotifynderCollection extends Collection
{
    /**
     * @var NotifynderTranslator
     */
    protected $notifynderTranslator;

    /**
     * @param array $models
     */
    public function __construct($models)
    {
        parent::__construct($models);
    }

    /**
     * Translator instance.
     *
     * @return NotifynderTranslator
     */
    public function translator()
    {
        return app('notifynder.translator');
    }

    /**
     * This method translate the body text from
     * another language. It used by collection.
     *
     * @param $language (String)
     * @return Collection
     */
    public function translate($language)
    {
        // Loop through the notifications
        foreach ($this->items as $key => $item) {
            try {
                $translation = $this->translator()
                    ->translate($language, $this->items[$key]['body']['name']);

                $this->items[$key]['body']['text'] = $translation;
            } catch (NotificationTranslationNotFoundException $e) {
                $this->items[$key]['body']['text'];
            }
        }

        $this->parse();

        return $this;
    }

    /**
     * Parse the body of the notification.
     *
     * @return $this
     */
    public function parse()
    {
        $parser = new NotifynderParser();

        foreach ($this->items as $key => $item) {
            $this->items[$key]['notify_body'] = $parser->parse($item);
            $this->items[$key]['text'] = $this->items[$key]['notify_body'];
        }

        return $this;
    }
}
