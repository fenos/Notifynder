<?php

use Fenos\Tests\Models\User;

class ConfigTest extends NotifynderTestCase
{
    public function testIsPolymorphic()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('bool', $config->isPolymorphic());
    }

    public function testIsStrict()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('bool', $config->isStrict());
    }

    public function testIsTranslated()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('bool', $config->isTranslated());
    }

    public function testGetNotifiedModel()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('string', $config->getNotifiedModel());
        $this->assertSame(User::class, $config->getNotifiedModel());
    }

    public function testGetNotifiedModelFail()
    {
        $this->expectException(InvalidArgumentException::class);

        $config = app('notifynder.config');
        $config->set('model', 'undefined_class_name');
        $config->getNotifiedModel();
    }

    public function testGetAdditionalFields()
    {
        $config = app('notifynder.config');
        $config->set('additional_fields.fillable', ['fillable_field']);
        $config->set('additional_fields.required', ['required_field']);
        $this->assertInternalType('array', $config->getAdditionalFields());
        $this->assertCount(2, $config->getAdditionalFields());
        $this->assertSame(['required_field', 'fillable_field'], $config->getAdditionalFields());
    }

    public function testGetAdditionalFieldsFillable()
    {
        $config = app('notifynder.config');
        $config->set('additional_fields.fillable', ['fillable_field']);
        $this->assertInternalType('array', $config->getAdditionalFields());
        $this->assertSame(['fillable_field'], $config->getAdditionalFields());
    }

    public function testGetAdditionalFieldsRequired()
    {
        $config = app('notifynder.config');
        $config->set('additional_fields.required', ['required_field']);
        $this->assertInternalType('array', $config->getAdditionalFields());
        $this->assertSame(['required_field'], $config->getAdditionalFields());
    }

    public function testGetAdditionalFieldsEmpty()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('array', $config->getAdditionalFields());
        $this->assertSame([], $config->getAdditionalFields());
    }

    public function testGetAdditionalRequiredFields()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('array', $config->getAdditionalRequiredFields());
        $this->assertSame([], $config->getAdditionalRequiredFields());
    }

    public function testGetTranslationDomain()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('string', $config->getTranslationDomain());
        $this->assertSame('notifynder', $config->getTranslationDomain());
    }

    public function testHasTrue()
    {
        $config = app('notifynder.config');
        $this->assertTrue($config->has('polymorphic'));
    }

    public function testHasFalse()
    {
        $config = app('notifynder.config');
        $this->assertFalse($config->has('undefined_config_key'));
    }

    public function testSet()
    {
        $config = app('notifynder.config');
        $config->set('polymorphic', true);
        $this->assertTrue($config->get('polymorphic'));
    }

    public function testGetOverloaded()
    {
        $config = app('notifynder.config');
        $this->assertInternalType('bool', $config->polymorphic);
    }

    public function testSetOverloaded()
    {
        $config = app('notifynder.config');

        $config->polymorphic = true;
        $this->assertInternalType('bool', $config->polymorphic);
        $this->assertTrue($config->get('polymorphic'));
    }
}
