<?php

namespace spec\Fenos\Notifynder\Translator;

use Fenos\Notifynder\Exceptions\NotificationLanguageNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationTranslationNotFoundException;
use Fenos\Notifynder\Translator\Compiler;
use Illuminate\Contracts\Config\Repository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TranslatorManagerSpec extends ObjectBehavior
{
    public function let(Compiler $compiler, Repository $config)
    {
        $this->beConstructedWith($compiler,$config);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Translator\TranslatorManager');
    }

    /** @test */
    function it_translate_the_given_category_in_the_given_language(Compiler $compiler, Repository $config)
    {
        $filePath = 'cached/file';
        $categoryToTranslate = 'categoryName';
        $translations = [
            'it' => [
                $categoryToTranslate => 'translation'
            ]
        ];

        $compiler->getFilePath()->shouldBeCalled()
            ->willReturn($filePath);

        $config->get('notifynder.translations')->shouldBeCalled()
            ->willReturn($translations);

        $compiler->cacheFile($translations)->shouldBeCalled();


        $this->translate('it', $categoryToTranslate)
             ->shouldReturn($translations['it'][$categoryToTranslate]);
    }

    /** @test */
    function it__try_to_translate_the_given_category(Compiler $compiler, Repository $config)
    {
        $filePath = 'cached/file';
        $categoryToTranslate = 'categoryName';
        $translations = [
            'it' => [
                $categoryToTranslate => 'translation'
            ]
        ];

        $compiler->getFilePath()->shouldBeCalled()
            ->willReturn($filePath);

        $config->get('notifynder.translations')->shouldBeCalled()
            ->willReturn($translations);

        $compiler->cacheFile($translations)->shouldBeCalled();

        $this->shouldThrow(NotificationTranslationNotFoundException::class)
             ->during('translate',['it','not existing']);
    }

    /** @test */
    function it_get_a_language_from_the_translations(Compiler $compiler, Repository $config)
    {
        $filePath = 'cached/file';
        $translations = [
            'it' => [
                'categoryName' => 'translation'
            ]
        ];

        $compiler->getFilePath()->shouldBeCalled()
            ->willReturn($filePath);

        $config->get('notifynder.translations')->shouldBeCalled()
            ->willReturn($translations);

        $compiler->cacheFile($translations)->shouldBeCalled();

        $this->getLanguage('it')->shouldReturn($translations['it']);
    }

    /** @test */
    function it__try_to_get_a_language_from_the_translations(Compiler $compiler, Repository $config)
    {
        $filePath = 'cached/file';
        $translations = [
            'it' => [
                'categoryName' => 'translation'
            ]
        ];

        $compiler->getFilePath()->shouldBeCalled()
            ->willReturn($filePath);

        $config->get('notifynder.translations')->shouldBeCalled()
            ->willReturn($translations);

        $compiler->cacheFile($translations)->shouldBeCalled();

        $this->shouldThrow(NotificationLanguageNotFoundException::class)
             ->during('getLanguage',['en']);
    }

    /** @test */
    function it_get_the_translations_from_never_cached_config_file(Compiler $compiler, Repository $config)
    {
        $filePath = 'cached/file';
        $translations = [];

        $compiler->getFilePath()->shouldBeCalled()
                 ->willReturn($filePath);

        $config->get('notifynder.translations')->shouldBeCalled()
                ->willReturn($translations);

        $compiler->cacheFile($translations)->shouldBeCalled();

        $this->getTranslations()->shouldReturn($translations);
    }
}
