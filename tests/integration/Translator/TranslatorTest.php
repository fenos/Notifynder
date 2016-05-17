<?php

use Fenos\Notifynder\Translator\TranslatorManager;

/**
 * Class TranslatorTest.
 */
class TranslatorTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var TranslatorManager
     */
    protected $translator;

    /**
     * Set Up.
     */
    public function setUp()
    {
        parent::setUp();

        $translations = require 'translations.php';

        app('config')->set('notifynder.translations', $translations);
        $this->translator = app('notifynder.translator');
    }

    /** @test */
    public function it_translate_a_notification()
    {
        $translation = $this->translator->translate('it', 'welcome');

        $this->assertEquals('benvenuto', $translation);
    }
}
