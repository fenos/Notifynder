<?php namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;

/**
 * Class NotifynderTranslator
 *
 * The Translator is responsable to translate the text
 * of the notifications with the custom languages that
 * you define in the configuration file. Translations
 * are cached in storage/app/notifynder in a json format.
 *
 * Usage:
 *
 * [
 *  'it' => [
 *      'name.category' => 'text to translate'
 *   ]
 * ]
 *
 * @package Fenos\Notifynder\Translator
 */
interface NotifynderTranslator
{

    /**
     * Translate the given category
     *
     * @param $language
     * @param $nameCategory
     * @return mixed
     * @throws NotificationLanguageNotFoundException
     * @throws NotificationTranslationNotFoundException
     */
    public function translate($language, $nameCategory);

    /**
     * Get selected language of tranlsations
     *
     * @param $language
     * @return mixed
     * @throws NotificationLanguageNotFoundException
     */
    public function getLanguage($language);

    /**
     * Get translations
     *
     * @return array|mixed
     */
    public function getTranslations();
}
