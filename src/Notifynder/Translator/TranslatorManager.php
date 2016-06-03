<?php

namespace Fenos\Notifynder\Translator;

use Fenos\Notifynder\Contracts\NotifynderTranslator;
use Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;
use Illuminate\Contracts\Config\Repository;

/**
 * Class NotifynderTranslator.
 *
 * The Translator is responsible to translate the text
 * of the notifications with the custom languages that
 * you'll define in the configuration file. Translations
 * are cached in storage/app/notifynder in a json format.
 *
 * Usage:
 *
 * [
 *  'it' => [
 *      'name.category' => 'text to {parse value} translate'
 *   ]
 * ]
 */
class TranslatorManager implements NotifynderTranslator
{
    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @param Compiler   $compiler
     * @param Repository $config
     */
    public function __construct(Compiler $compiler, Repository $config)
    {
        $this->compiler = $compiler;
        $this->config = $config;
    }

    /**
     * Translate the given category.
     *
     * @param $language
     * @param $nameCategory
     * @return mixed
     * @throws NotificationLanguageNotFoundException
     * @throws NotificationTranslationNotFoundException
     */
    public function translate($language, $nameCategory)
    {
        $translations = $this->getLanguage($language);

        if (array_key_exists($nameCategory, $translations)) {
            return $translations[$nameCategory];
        }

        $error = 'Translation not found';
        throw new NotificationTranslationNotFoundException($error);
    }

    /**
     * Get selected language of translations.
     *
     * @param $language
     * @return mixed
     * @throws NotificationLanguageNotFoundException
     */
    public function getLanguage($language)
    {
        $translations = $this->getTranslations();

        if (array_key_exists($language, $translations)) {
            return $translations[$language];
        }

        $error = 'Language Not Found';
        throw new NotificationLanguageNotFoundException($error);
    }

    /**
     * Get translations.
     *
     * @return array|mixed
     */
    public function getTranslations()
    {
        // File cached path
        $filePath = $this->compiler->getFilePath();

        // If the file exists
        if (file_exists($filePath)) {
            // Check if is not expired
            if (! $this->compiler->isExpired()) {
                // Return the cached file in
                // an array
                return json_decode(
                    file_get_contents($filePath)
                );
            }
        }

        return $this->cacheFromConfig();
    }

    /**
     * Get the translations from the
     * array of the config file and it
     * will cache them.
     *
     * @return array
     */
    protected function cacheFromConfig()
    {
        // If is expire then I retrieve directly the array
        $fileTranslation = $this->config->get(
            'notifynder.translations'
        );

        // I put the edited content in the cached file
        $this->compiler->cacheFile($fileTranslation);

        // return the translations
        return $fileTranslation;
    }
}
