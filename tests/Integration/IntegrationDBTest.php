<?php

use Laracasts\TestDummy\Factory as TestDummy;

abstract class IntegrationDBTest extends Orchestra\Testbench\TestCase
{

    /**
     * Add package Provider
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return array(
            'Fenos\Notifynder\NotifynderServiceProvider',
        );
    }

    /**
     * Add package Alias
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return array(
            'Notifynder' => 'Fenos\Notifynder\Facades\Notifynder',
        );
    }

    /**
     * Set up the enviroment database settings
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__.'/../../src';

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }

    /**
     * Set Up Configuration for Integration
     * Test with Database
     */
    public function setUp()
    {
        parent::setUp();

        $artisan = $this->app->make('artisan');

        $artisan->call('migrate', array(
            '--database' => 'testbench',
            '--path' => 'migrations',
            '--bench'    => 'fenos/notifynder',
        ));

        // I created the migration on the tests directory
        // for make sure that the user table is present during the tests
        $artisan->call('migrate', array(
            '--database' => 'testbench',
            '--path'     => '../tests/Integration/tests_migrations',
            '--bench'    => 'fenos/notifynder',
        ));

        // Change path of the test dummy directory
        // it now search in the tests directory of the package
        TestDummy::setFixtures(base_path()."/../tests");
    }
}
