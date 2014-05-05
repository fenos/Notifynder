<?php namespace Fenos\Notifynder;

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
		$this->repositories();
		$this->notifynder();
		$this->collections();
		$this->handler();
	}

	/**
	* Bind Notifynder Main Class
	*/
	public function notifynder()
	{
		$this->app['notifynder'] = $this->app->share(function($app)
    	{
	        return new Notifynder(
	        	$app->make('Fenos\Notifynder\Repositories\EloquentRepository\NotifynderEloquentRepositoryInterface'),
	        	$app->make('notifynder.repository.category'),
	        	$app->make('Fenos\Notifynder\Translator\NotifynderTranslator'),
	        	$app->make('notifynder.handler')
	        );
    	});
	}

	/**
	* Bind repositories
	*/
	public function repositories()
	{	

		// bind NotifynderRepository
		$this->app->bind('notifynder.repository.notifynder', function($app){

			$classRepo = ( $this->app['config']->get('notifynder::config.polymorphic') === true) ? "NotifynderRepositoryPolymorphic" : "NotifynderRepository";
			$generateRepository = "Fenos\Notifynder\Repositories\EloquentRepository\\".$classRepo;

			$notification_model = $app['config']->get('notifynder::config.notification_model');
			

			return new $generateRepository(
				new $notification_model,
				$app['db']
			);
		});

		$this->app->bind('Fenos\Notifynder\Repositories\EloquentRepository\NotifynderEloquentRepositoryInterface','notifynder.repository.notifynder');
		

		// bind NotifynderCategoryRepository
		$this->app->bind('notifynder.repository.category', function($app){

			return new Repositories\EloquentRepository\NotifynderCategoryRepository(
				new Models\NotificationCategory,
				$app['db']
			);
		});
	}

	/**
	* Bind Collections
	*/
	public function collections()
	{
		$this->app->bind('Fenos\Notifynder\Models\Collections\NotifynderTranslationCollection', function($app){

			return new Models\Collections\NotifynderTranslationCollection(
				[],
				$app->make('Fenos\Notifynder\Translator\NotifynderTranslator')
			);

		});
	}

	public function handler()
	{
		$this->app->bind('notifynder.handler', function($app){

			return new Handler\NotifynderHandler(
				$app['app']
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