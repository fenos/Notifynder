<?php namespace Fenos\Notifynder\Translator;

use Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;

/**
*
*/
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

    function __construct()
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
     * translation from the array
     *
     * @param $language
     * @param $name
     * @return mixed
     * @throws \Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException
     */
    public function translate($language,$name)
    {
        $language = $this->getLanguage($language);

        if ( array_key_exists($name, $language) )
        {
            return $language[$name];
        }

        throw new NotificationTranslationNotFoundException("Translation not found");
    }

    /**
     * Get language from the array
     *
     * @param $language
     * @return mixed
     * @throws \Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException
     */
    public function getLanguage($language)
    {
        if ( array_key_exists($language, $this->languages) )
        {
            return $this->languages[$language];
        }

        throw new NotificationLanguageNotFoundException("Language Not Found");
    }

    /**
    * Load the array content the translations
    *
    * @return Array
    */
    public function loadTranslations()
    {
        if ( !is_null( $this->languages ) )
        {
            return $this->languages;
        }

        return $this->languages = $this->app['config']->get('notifynder::config.translation_file');
    }
}
