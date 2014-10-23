<?php namespace Fenos\Notifynder;

use Fenos\Notifynder\Artisan\CategoryAdd;
use Fenos\Notifynder\Artisan\CategoryDelete;
use Fenos\Notifynder\Artisan\GroupAdd;
use Fenos\Notifynder\Artisan\GroupAddCategories;
use Fenos\Notifynder\Categories\NotifynderCategory;
use Fenos\Notifynder\Categories\Repositories\NotifynderCategoryDB;
use Fenos\Notifynder\Groups\NotifynderGroup;
use Fenos\Notifynder\Groups\Repositories\NotificationGroupCategoryRepository;
use Fenos\Notifynder\Groups\Repositories\NotificationGroupsRepository;
use Fenos\Notifynder\Handler\NotifynderHandler;
use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Models\NotificationCategory;
use Fenos\Notifynder\Models\NotificationGroup;
use Fenos\Notifynder\Notifications\NotifynderNotification;
use Fenos\Notifynder\Notifications\Repositories\NotificationRepository;
use Fenos\Notifynder\Parse\ArtisanOptionsParser;
use Fenos\Notifynder\Senders\NotifynderSender;
use Fenos\Notifynder\Senders\NotifynderSenderFactory;
use Fenos\Notifynder\Senders\Queue\NotifynderQueue;
use Illuminate\Support\ServiceProvider;

class NotifynderServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('fenos/notifynder');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->notifynder();
        $this->notifynderNotification();
        $this->notifynderCategory();
        $this->notifynderSender();
        $this->notifynderGroup();
        $this->notifynderHandler();

        // Commands
        $this->CategoryAddCommand();
        $this->CategoryDeleteCommand();
        $this->GroupAddCommand();
        $this->GroupAddCategoriesCommand();
    }

    /**
     * Register Notifynder
     */
    protected function notifynder()
    {
        $this->app['notifynder'] = $this->app->share(function ($app)
        {
            /** @var $app \Illuminate\Foundation\Application */
            return new Notifynder(
                $app->make('notifynder.category'),
                $app->make('notifynder.sender'),
                $app->make('notifynder.notification'),
                $app->make('notifynder.hanlder'),
                $app->make('notifynder.group')
            );
        });
    }

    /**
     * Register NotifynderNotification
     */
    private function notifynderNotification()
    {
        $this->app['notifynder.notification'] = $this->app->share(function($app){

            /** @var $app \Illuminate\Foundation\Application */
            return new NotifynderNotification(
                  $app->make('notifynder.notification.repository')
                );
        });

        $this->app['notifynder.notification.repository'] = $this->app->share(function($app){

            $model = $app['config']->get('notifynder::config.model');

            /** @var $app \Illuminate\Foundation\Application */
            return new NotificationRepository(
                    new $model,
                    $app['db']
                );
        });

        $this->app->bind(
            'Fenos\Notifynder\Notifications\Repositories\NotificationEloquent',
            'notifynder.notification.repository'
        );
    }

    /**
     * Register Notifynder Category
     */
    private function notifynderCategory()
    {
        $this->app['notifynder.category'] = $this->app->share(function($app)
        {
             /** @var $app \Illuminate\Foundation\Application */
            return new NotifynderCategory(
              $app->make('notifynder.category.repository')
            );
        });

        $this->app['notifynder.category.repository'] = $this->app->share(function()
        {
            return new NotifynderCategoryDB(
                new NotificationCategory()
            );
        });

        $this->app->bind(
            'Fenos\Notifynder\Categories\Repositories\CategoryRepository',
            'notifynder.category.repository'
        );
    }

    private function notifynderGroup()
    {
        $this->app['notifynder.group'] = $this->app->share(function($app){
                return new NotifynderGroup(
                    $app->make('notifynder.group.repository'),
                    $app->make('notifynder.group.category-repository')
                );
        });

        $this->app['notifynder.group.repository'] = $this->app->share(function($app){
                return new NotificationGroupsRepository(
                  new NotificationGroup()
                );
         });

        $this->app['notifynder.group.category-repository'] = $this->app->share(function($app){
            return new NotificationGroupCategoryRepository(
                $app->make('notifynder.category'),
                new NotificationGroup()
            );
        });
    }

    /**
     * Register Senders
     */
    private function notifynderSender()
    {
        $this->app['notifynder.sender'] = $this->app->share(function($app){

            /** @var $app \Illuminate\Foundation\Application */
            return new NotifynderSender(
                  $app->make('notifynder.sender.factory'),
                  $app->make('notifynder.notification'),
                  $app->make('notifynder.sender.queue')
                );
        });

        $this->app['notifynder.sender.factory'] = $this->app->share(function($app){
                return new NotifynderSenderFactory(
                    $app->make('notifynder.group')
                );
        });

        $this->app['notifynder.sender.repository'] = $this->app->share(function($app){

            $model = $app['config']->get('notifynder::config.model');

            /** @var $app \Illuminate\Foundation\Application */
            return new NotificationRepository(
                  new $model,
                  $app['db']
                );
        });

        $this->app['notifynder.sender.queue'] = $this->app->share(function($app){
                return new NotifynderQueue(
                  $app['config'],
                  $app['queue']
                );
         });
    }

    /**
     * Register Category Add Command
     */
    private function CategoryAddCommand()
    {
        $this->app['notifynder.category-add'] = $this->app->share(function($app)
        {
            /** @var $app \Illuminate\Foundation\Application */
            return new CategoryAdd(
                $app->make('notifynder.category')
            );
        });

        $this->commands('notifynder.category-add');
    }

    /**
     * Register Category Delete Command
     */
    private function CategoryDeleteCommand()
    {
        $this->app['notifynder.category-delete'] = $this->app->share(function($app)
        {
            /** @var $app \Illuminate\Foundation\Application */
            return new CategoryDelete(
                $app->make('notifynder.category')
            );
        });

        $this->commands('notifynder.category-delete');
    }

    private function GroupAddCommand()
    {
        $this->app['notifynder.group-add'] = $this->app->share(function($app)
        {
            /** @var $app \Illuminate\Foundation\Application */
            return new GroupAdd(
                $app->make('notifynder.group')
            );
        });


        $this->commands('notifynder.group-add');
    }

    private function GroupAddCategoriesCommand()
    {
        $this->app['notifynder.group-add-categories'] = $this->app->share(function($app)
        {
            /** @var $app \Illuminate\Foundation\Application */
            return new GroupAddCategories(
                $app->make('notifynder.group'),
                new ArtisanOptionsParser()
            );
        });


        $this->commands('notifynder.group-add-categories');
    }

    /**
     * Notifynder Handler
     */
    private function notifynderHandler()
    {
        $this->app['notifynder.hanlder'] = $this->app->share(function($app){
            /** @var $app \Illuminate\Foundation\Application */
            return new NotifynderHandler(
                $app['events'],
                $app['config']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
