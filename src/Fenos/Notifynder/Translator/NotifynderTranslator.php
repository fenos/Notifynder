<?php

namespace Fenos\Notifynder\Translator;

use Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;

class NotifynderTranslator
{
    /**
     * @var array
     */
    protected $languages = [];

    /**
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    public function __construct()
    {
        $this->app = app();
        $this->loadTranslations();
    }

    /**
     * @return mixed
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * Translate the notification getting the
     * translation from the array.
     *
     * @param $language
     * @param $name
     *
     * @throws \Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException
     *
     * @return mixed
     */
    public function translate($language, $name)
    {
        $language = $this->getLanguage($language);

        if (array_key_exists($name, $language)) {
            return $language[$name];
        }

        throw new NotificationTranslationNotFoundException('Translation not found');
    }

    /**
     * Get language from the array.
     *
     * @param $language
     *
     * @throws \Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException
     *
     * @return mixed
     */
    public function getLanguage($language)
    {
        if (array_key_exists($language, $this->languages)) {
            return $this->languages[$language];
        }

        throw new NotificationLanguageNotFoundException('Language Not Found');
    }

    /**
     * Load the array content the translations.
     *
     * @return array
     */
    public function loadTranslations()
    {
        if (!is_null($this->languages)) {
            return $this->languages;
        }

        return $this->languages = $this->app['config']->get('notifynder::config.translation_file');
    }
}
