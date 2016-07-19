<?php

namespace Fenos\Notifynder;

use Fenos\Notifynder\Collections\Config;
use Fenos\Notifynder\Contracts\ConfigContract;
use Fenos\Notifynder\Contracts\NotifynderManagerContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Fenos\Notifynder\Managers\NotifynderManager;
use Fenos\Notifynder\Managers\SenderManager;
use Fenos\Notifynder\Senders\MultipleSender;
use Fenos\Notifynder\Senders\OnceSender;
use Fenos\Notifynder\Senders\SingleSender;
use Illuminate\Support\ServiceProvider;

class NotifynderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindContracts();
        $this->bindConfig();
        $this->bindSender();
        $this->bindNotifynder();

        $this->registerSenders();
    }

    public function boot()
    {
        $this->config();
        $this->migration();
    }

    /**
     * Bind contracts.
     */
    protected function bindContracts()
    {
        $this->app->bind(NotifynderManagerContract::class, 'notifynder');
        $this->app->bind(SenderManagerContract::class, 'notifynder.sender');
        $this->app->bind(ConfigContract::class, 'notifynder.config');
    }

    /**
     * Bind notifynder config.
     */
    protected function bindConfig()
    {
        $this->app->singleton('notifynder.config', function ($app) {
            return new Config();
        });
    }

    /**
     * Bind notifynder config.
     */
    protected function bindSender()
    {
        $this->app->singleton('notifynder.sender', function ($app) {
            return new SenderManager();
        });
    }

    /**
     * Bind notifynder manager.
     */
    protected function bindNotifynder()
    {
        $this->app->singleton('notifynder', function ($app) {
            return new NotifynderManager(
                $app['notifynder.sender']
            );
        });
    }

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
     * Publish config file.
     */
    protected function config()
    {
        $this->publishes([
            __DIR__ . '/../config/notifynder.php' => config_path('notifynder.php'),
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/notifynder.php', 'notifynder');
    }

    /**
     * Publish migration files.
     */
    protected function migration()
    {
        if (!class_exists('NotificationCategories')) {
            $this->publishMigration('2014_02_10_145728_notification_categories');
        }
        if (!class_exists('CreateNotificationGroupsTable')) {
            $this->publishMigration('2014_08_01_210813_create_notification_groups_table');
        }
        if (!class_exists('CreateNotificationCategoryNotificationGroupTable')) {
            $this->publishMigration('2014_08_01_211045_create_notification_category_notification_group_table');
        }
        if (!class_exists('CreateNotificationsTable')) {
            $this->publishMigration('2015_05_05_212549_create_notifications_table');
        }
        if (!class_exists('AddExpireTimeColumnToNotificationTable')) {
            $this->publishMigration('2015_06_06_211555_add_expire_time_column_to_notification_table');
        }
        if (!class_exists('ChangeTypeToExtraInNotificationsTable')) {
            $this->publishMigration('2015_06_06_211555_change_type_to_extra_in_notifications_table');
        }
        if (!class_exists('AlterCategoryNameToUnique')) {
            $this->publishMigration('2015_06_07_211555_alter_category_name_to_unique');
        }
        if (!class_exists('MakeNotificationUrlNullable')) {
            $this->publishMigration('2016_04_19_200827_make_notification_url_nullable');
        }
        if (!class_exists('AddStackIdToNotifications')) {
            $this->publishMigration('2016_05_19_144531_add_stack_id_to_notifications');
        }
        if (!class_exists('UpdateVersion4NotificationsTable')) {
            $this->publishMigration('2016_07_01_153156_update_version4_notifications_table');
        }
    }

    /**
     * @param string $filename
     */
    protected function publishMigration($filename)
    {
        $extension = '.php';
        $filename = trim($filename, $extension) . $extension;
        $stub = __DIR__ . '/../migrations/' . $filename;
        $target = $this->migrationFilepath($filename);
        $this->publishes([$stub => $target], 'migrations');
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function migrationFilepath($filename)
    {
        if (function_exists('database_path')) {
            return database_path('/migrations/' . $filename);
        } else {
            return base_path('/database/migrations/' . $filename);
        }
    }
}
