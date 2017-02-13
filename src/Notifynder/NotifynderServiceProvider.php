<?php

namespace Fenos\Notifynder;

use Illuminate\Support\ServiceProvider;
use Fenos\Notifynder\Collections\Config;
use Fenos\Notifynder\Senders\OnceSender;
use Fenos\Notifynder\Senders\SingleSender;
use Fenos\Notifynder\Managers\SenderManager;
use Fenos\Notifynder\Senders\MultipleSender;
use Fenos\Notifynder\Resolvers\ModelResolver;
use Fenos\Notifynder\Contracts\ConfigContract;
use Fenos\Notifynder\Managers\NotifynderManager;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Fenos\Notifynder\Contracts\NotifynderManagerContract;

/**
 * Class NotifynderServiceProvider.
 */
class NotifynderServiceProvider extends ServiceProvider
{
    protected $migrations = [
        'NotificationCategories' => '2014_02_10_145728_notification_categories',
        'CreateNotificationGroupsTable' => '2014_08_01_210813_create_notification_groups_table',
        'CreateNotificationCategoryNotificationGroupTable' => '2014_08_01_211045_create_notification_category_notification_group_table',
        'CreateNotificationsTable' => '2015_05_05_212549_create_notifications_table',
        'AddExpireTimeColumnToNotificationTable' => '2015_06_06_211555_add_expire_time_column_to_notification_table',
        'ChangeTypeToExtraInNotificationsTable' => '2015_06_06_211555_change_type_to_extra_in_notifications_table',
        'AlterCategoryNameToUnique' => '2015_06_07_211555_alter_category_name_to_unique',
        'MakeNotificationUrlNullable' => '2016_04_19_200827_make_notification_url_nullable',
        'AddStackIdToNotifications' => '2016_05_19_144531_add_stack_id_to_notifications',
        'UpdateVersion4NotificationsTable' => '2016_07_01_153156_update_version4_notifications_table',
        'DropVersion4UnusedTables' => '2016_11_02_193415_drop_version4_unused_tables',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindContracts();
        $this->bindConfig();
        $this->bindSender();
        $this->bindResolver();
        $this->bindNotifynder();

        $this->registerSenders();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->config();
        $this->migration();
    }

    /**
     * Bind contracts.
     *
     * @return void
     */
    protected function bindContracts()
    {
        $this->app->bind(NotifynderManagerContract::class, 'notifynder');
        $this->app->bind(SenderManagerContract::class, 'notifynder.sender');
        $this->app->bind(ConfigContract::class, 'notifynder.config');
    }

    /**
     * Bind Notifynder config.
     *
     * @return void
     */
    protected function bindConfig()
    {
        $this->app->singleton('notifynder.config', function () {
            return new Config();
        });
    }

    /**
     * Bind Notifynder sender.
     *
     * @return void
     */
    protected function bindSender()
    {
        $this->app->singleton('notifynder.sender', function () {
            return new SenderManager();
        });
    }

    /**
     * Bind Notifynder resolver.
     *
     * @return void
     */
    protected function bindResolver()
    {
        $this->app->singleton('notifynder.resolver.model', function () {
            return new ModelResolver();
        });
    }

    /**
     * Bind Notifynder manager.
     *
     * @return void
     */
    protected function bindNotifynder()
    {
        $this->app->singleton('notifynder', function ($app) {
            return new NotifynderManager(
                $app['notifynder.sender']
            );
        });
    }

    /**
     * Register the default senders.
     *
     * @return void
     */
    public function registerSenders()
    {
        app('notifynder')->extend('sendSingle', function (array $notifications) {
            return new SingleSender($notifications);
        });

        app('notifynder')->extend('sendMultiple', function (array $notifications) {
            return new MultipleSender($notifications);
        });

        app('notifynder')->extend('sendOnce', function (array $notifications) {
            return new OnceSender($notifications);
        });
    }

    /**
     * Publish and merge config file.
     *
     * @return void
     */
    protected function config()
    {
        $this->publishes([
            __DIR__.'/../config/notifynder.php' => config_path('notifynder.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/notifynder.php', 'notifynder');
    }

    /**
     * Publish migration files.
     *
     * @return void
     */
    protected function migration()
    {
        foreach ($this->migrations as $class => $file) {
            if (! class_exists($class)) {
                $this->publishMigration($file);
            }
        }
    }

    /**
     * Publish a single migration file.
     *
     * @param string $filename
     * @return void
     */
    protected function publishMigration($filename)
    {
        $extension = '.php';
        $filename = trim($filename, $extension).$extension;
        $stub = __DIR__.'/../migrations/'.$filename;
        $target = $this->getMigrationFilepath($filename);
        $this->publishes([$stub => $target], 'migrations');
    }

    /**
     * Get the migration file path.
     *
     * @param string $filename
     * @return string
     */
    protected function getMigrationFilepath($filename)
    {
        if (function_exists('database_path')) {
            return database_path('/migrations/'.$filename);
        }

        return base_path('/database/migrations/'.$filename); // @codeCoverageIgnore
    }
}
