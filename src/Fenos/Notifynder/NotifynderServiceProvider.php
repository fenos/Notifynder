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
		
	}

	/**
	* Bind Notifynder Main Class
	*/
	public function notifynder()
	{
		$this->app['notifynder'] = $this->app->share(function($app)
    	{
	        return new Notifynder(
	        	$app->make('notifynder.repository.notifynder'),
	        	$app->make('notifynder.repository.category'),
	        	$app->make('Fenos\Notifynder\Translator\NotifynderTranslator')
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

			$notification_model = $app['config']->get('notifynder::config.notification_model');

			return new Repositories\EloquentRepository\NotifynderRepository(
				new $notification_model,
				$app['db']
			);
		});

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