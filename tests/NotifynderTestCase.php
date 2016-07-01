<?php

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Fenos\Tests\Models\User;

abstract class NotifynderTestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return ['Fenos\Notifynder\NotifynderServiceProvider'];
    }

    public function setUp()
    {
        parent::setUp();
        // This should only do work for Sqlite DBs in memory.
        $artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        app('db')->beginTransaction();
        $this->migrate($artisan);
        $this->migrate($artisan, '/../../../../tests/migrations');
        // Set up the User Test Model
        app('config')->set('notifynder.notification_model', 'Fenos\Notifynder\Models\Notification');
        app('config')->set('notifynder.model', 'Fenos\Tests\Models\User');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function tearDown()
    {
        app('db')->rollback();
    }

    protected function getApplicationTimezone($app)
    {
        return 'UTC';
    }

    protected function migrate($artisan, $path = '/../../../../src/migrations')
    {
        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--path'     => $path,
        ]);
    }

    protected function createUser(array $attributes = [])
    {
        $attributes = array_merge([
            'id' => 1,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ], $attributes);

        return User::create($attributes);
    }
}
