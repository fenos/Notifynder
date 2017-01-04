<?php

use Fenos\Tests\Models\Car;
use Fenos\Tests\Models\User;
use Fenos\Tests\Models\CarL53;
use Fenos\Tests\Models\UserL53;
use Illuminate\Database\Eloquent\Model;
use Fenos\Notifynder\NotifynderServiceProvider;
use Fenos\Notifynder\Models\NotificationCategory;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Fenos\Notifynder\Facades\Notifynder as NotifynderFacade;

abstract class NotifynderTestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            NotifynderServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Notifynder' => NotifynderFacade::class,
        ];
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
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
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
            '--path' => $path,
        ]);
    }

    protected function createCategory(array $attributes = [])
    {
        $attributes = array_merge([
            'text' => 'Notification send from #{from.id} to #{to.id}.',
            'name' => 'test.category',
        ], $attributes);

        return NotificationCategory::create($attributes);
    }

    protected function createUser(array $attributes = [])
    {
        $attributes = array_merge([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ], $attributes);

        if ($this->getLaravelVersion() < 5.3) {
            return User::create($attributes);
        } else {
            return UserL53::create($attributes);
        }
    }

    protected function createCar(array $attributes = [])
    {
        $attributes = array_merge([
            'brand' => 'Audi',
            'model' => 'A6',
        ], $attributes);

        if ($this->getLaravelVersion() < 5.3) {
            return Car::create($attributes);
        } else {
            return CarL53::create($attributes);
        }
    }

    protected function sendNotificationTo(Model $model)
    {
        return $model
            ->sendNotificationTo(1)
            ->from(2)
            ->send();
    }

    protected function sendNotificationsTo(Model $model, $amount = 10)
    {
        while ($amount > 0) {
            $this->sendNotificationTo($model);
            $amount--;
        }
    }

    protected function getLaravelVersion()
    {
        $version = app()->version();
        $parts = explode('.', $version);

        return ($parts[0].'.'.$parts[1]) * 1;
    }
}
