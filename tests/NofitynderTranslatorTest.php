<?php

use Mockery as m;
use Fenos\Notifynder\Translator\NotifynderTranslator;

/**
* Test NofitynderTranslatorTest Class
*/
class NofitynderTranslatorTest extends PHPUnit_Framework_TestCase
{
    /**
    * @var
    */
    protected $notifynderTranslator;

    /**
    * @var
    */
    protected $array_languages;

    /**
    * @var
    */
    protected $app;

    public function setUp()
    {

        $app = $this->mockApp();

        $this->notifynderTranslator = m::mock('Fenos\Notifynder\Translator\NotifynderTranslator')->makePartial();

        // mock app
        $this->notifynderTranslator->app = $app;

        // get array languages
        $this->languages = $this->array_languages();

        // set reflection class for set the protected variable $languages
        // with the array of our test
        $translator = $this->notifynderTranslator;
        $reflection = new \ReflectionClass($translator);
        $reflection_property = $reflection->getProperty('languages');
        $reflection_property->setAccessible(true);

        $reflection_property->setValue($translator, $this->languages);
    }

    /**
    * Tear down function for all tests
    *
    */
    public function teardown()
    {
        m::close();
    }

    public function test_traslate_notification()
    {
        $this->notifynderTranslator->shouldReceive('getLanguage')
                                    ->with('en')
                                    ->once()
                                    ->andReturn($this->languages['en']);

        $result = $this->notifynderTranslator->translate('en','notifynder');

        $this->assertEquals($result,'notifynder do the job well');
    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException
    */
    public function test_translate_notification_but_not_tranlsations_has_found()
    {
        $this->notifynderTranslator->shouldReceive('getLanguage')
                                    ->with('en')
                                    ->once()
                                    ->andReturn($this->languages['en']);

        $result = $this->notifynderTranslator->translate('en','message');

    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException
    */
    public function test_translate_notification_using_a_languages_that_does_not_exist()
    {
        $this->notifynderTranslator->shouldReceive('getLanguage')
                                    ->with('it')
                                    ->once()
                                    ->andThrow(new Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException);

        $result = $this->notifynderTranslator->translate('it','message');
    }

    public function test_get_languages_notification()
    {


        $result = $this->notifynderTranslator->getLanguage('en');

        $this->assertEquals($result,$this->languages['en']);
    }

    public function test_load_translations_array()
    {
        $result = $this->notifynderTranslator->loadTranslations();

        $this->assertEquals($this->languages,$result);
    }

    public function mockApp()
    {
        $app = array();

        $app['config'] = m::mock( 'Config' );

        $app['config']->shouldReceive( 'get' )
            ->with( 'notifynder::config.translation_file' )
            ->andReturn( m::type('array') );

        return $app;

    }

    public function array_languages()
    {
        return [

            'en' => [

                'notifynder' => 'notifynder do the job well'

            ]

        ];
    }

}
