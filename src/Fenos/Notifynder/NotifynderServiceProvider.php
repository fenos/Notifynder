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
	        	$app->make('notifynder.repository.category')
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

			return new Repositories\EloquentRepository\NotifynderRepository(
				new Models\Notification,
				$app['db']
			);
		});

		// trigger class NotifynderRepository when invoke NotifynderRepositoryInterface
		// $this->app->bind(
		// 		'Fenos\Notifynder\Repositories\EloquentRepository\NotifynderRepositoryInterface',
		// 		'Fenos\Notifynder\Repositories\EloquentRepository\NotifynderRepository'
		// );

		// bind NotifynderCategoryRepository
		$this->app->bind('notifynder.repository.category', function($app){

			return new Repositories\EloquentRepository\NotifynderCategoryRepository(
				new Models\NotificationCategory,
				$app['db']
			);
		});

		// // trigger class NotifynderCategoryRepository when invoke NotifynderCategoryRepositoryInterface
		// $this->app->bind(
		// 		'Fenos\Notifynder\Repositories\EloquentRepository\NotifynderCategoryRepositoryInterface',
		// 		'Fenos\Notifynder\Repositories\EloquentRepository\NotifynderCategoryRepository'
		// );
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