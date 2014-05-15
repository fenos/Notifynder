<?php namespace Fenos\Notifynder;

use Fenos\Notifynder\Repositories\EloquentRepository\NotifynderEloquentRepositoryInterface;
use Fenos\Notifynder\Repositories\EloquentRepository\NotifynderCategoryRepository;
use Fenos\Notifynder\Translator\NotifynderTranslator;
use Fenos\Notifynder\Handler\NotifynderHandler;

//Exceptions
use Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;

/**
* Class management for notifications
* Version 1.4.5 MIT Licence
*/
class Notifynder implements NotifynderInterface
{
	/**
	* @var NotifynderEloquentRepositoryInterface
	*/
	protected $notifynderRepository;

	/**
	* @var NotifynderCategoryRepository
	*/
	protected $notifynderCategoryRepository;

	/**
	* @var NotifynderTranslator
	*/
	protected $notifynderTranslator;

	/**
	* @var NotifynderHandler
	*/
	protected $notifynderHandler;

	/**
	* @var $category_container
	*/
	protected $category_container = array();

	/**
	* @var $entity
	*/
	protected $entity;

	/**
	* @var $category
	*/
	protected $category;

	function __construct(NotifynderEloquentRepositoryInterface $notifynderRepository,
						 NotifynderCategoryRepository $notifynderCategoryRepository,
						 NotifynderTranslator $notifynderTranslator,
						 NotifynderHandler $notifynderHandler	 )
	{
		$this->notifynderRepository = $notifynderRepository;
		$this->notifynderCategoryRepository = $notifynderCategoryRepository;
		$this->notifynderTranslator = $notifynderTranslator;
		$this->notifynderHandler = $notifynderHandler;
	}

    /**
     * Get id category by name given
     *
     * @param $name (String)
     * @throws Exceptions\NotificationCategoryNotFoundException
     * @return $this | NotificationCategoryNotFoundException
     */
	public function category($name)
	{	
		// lazy loading on getting notifications
		// for prevent multiples query on the same category
		if ( count($this->category_container ) > 0 )
		{
			if ( array_key_exists($name, $this->category_container) )
			{
				$this->category = $this->category_container[$name];

				return $this;
			}
		}
		// do query for get id category
		$category = $this->notifynderCategoryRepository->findByName($name);

		if ( !is_null($category) )
		{
			// add on array for lazy loading
			$this->category_container[$name] = $category->id;
			// id that will be used on the next action
			$this->category = $category->id;

			return $this;
		}

		throw new NotificationCategoryNotFoundException("Notification Category Not Found");
	}

	/**
	* Method used when polymorphic relations
	* are enabled. With this method you'll choose 
	* The model that you want get result
	*/
	public function entity($name)
	{
		if (class_exists($name))
		{
			$this->entity = $name;

			return $this;
		}

		throw new \InvalidArgumentException("The class [$name] not exist", 1);
		
	}

    /**
     * Send the notification to the current
     * User
     *
     * @param array $notificationInformations (Array)
     * @throws Exceptions\NotificationCategoryNotFoundException
     * @return \Fenos\Notifynder\Models\Notification
     */
	public function sendOne(array $notificationInformations)
	{
		// if you have specificated the category_id will be overwritten
		// even if you set up the method category()
		if ( !array_key_exists('category_id', $notificationInformations) ) 
		{
			// if you don't specificated the category_id but you used the category()
			// method let's set up it for the insert
			if ( !is_null( $this->category ) )
			{
				$notificationInformations['category_id'] = $this->category;
			}
			// if you don't specificated the category_id or used the method category
			// you'll receive the excpetion
			else
			{
				throw new NotificationCategoryNotFoundException("Notification Category Not Found please provide one");
			}
		}

		return $this->notifynderRepository->sendOne($notificationInformations);
	}

    /**
     * Send Multiple notifications at once
     * Remember to add manually the dateTime
     * Because the method Insert of DB doesn't
     * automatically
     *
     * @param array $multipleNotifications
     * @internal param $multiNotifications (Array)
     * @return Boolean
     */
	public function sendMultiple(array $multipleNotifications)
	{
		return $this->notifynderRepository->sendMultiple($multipleNotifications);
	}

    /**
     * Make read one notification
     *
     * @param $notification_id (int)
     * @throws Exceptions\NotificationNotFoundException
     * @return \Fenos\Notifynder\Models\Notification | False
     */
	public function readOne($notification_id)
	{
		if ( $notificationRead = $this->notifynderRepository->readOne($notification_id) )
		{
			return $notificationRead;
		}

		throw new NotificationNotFoundException("Notification Not Found");
		
	}

	/**
	* Read notifications in base the number
	* Given 
	*
	* @param $to_id 	(int)
	* @param $numbers	(int)
	* @param $position 	(String) | ASC - DESC
	*/
	public function readLimit($to_id,$numbers, $position = "ASC")
	{
		return $this->notifynderRepository->entity($this->entity)->readLimit($to_id,$numbers,$position);
	}

	/**
	* Make read all notification not read
	*
	* @param $to_id 	(int)
	* @return Number of notifications read (int)
	*/
	public function readAll($to_id)
	{
		return $this->notifynderRepository->entity($this->entity)->readAll($to_id);
	}

	/**
	* Retrive notifications not Read
	* You can also limit the number of
	* Notification if you don't it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return \Fenos\Notifynder\Models\Notification
	*/
	public function getNotRead($to_id,$limit = null, $paginate = false)
	{	
		return $this->notifynderRepository->entity($this->entity)->getNotRead($to_id, $limit, $paginate );
	}

	/**
	* Retrive all notifications not read
	* in first.
	* You can also limit the number of
	* Notification if you don't it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return \Fenos\Notifynder\Models\Notification
	*/
	public function getAll($to_id,$limit = null, $paginate = false)
	{
		return $this->notifynderRepository->entity($this->entity)->getAll($to_id,$limit,$paginate);
	}

	/**
	* Delete a notification giving the id
	* of it
	*
	* @param $id (int)
	* @return Boolean
	*/
	public function delete($id)
	{
		return $this->notifynderRepository->delete($id);
	}

	/**
	* Delete All notifications about the
	* current user 
	*
	* @param $user_id (int)
	* @return Boolean
	*/
	public function deleteAll($user_id)
	{
		return $this->notifynderRepository->entity($this->entity)->deleteAll($user_id);
	}

	/**
	* Delete numbers of notifications equals
	* to the number passing as 2 parameter of
	* the current user
	*
	* @param $user_id 	(int)
	* @param $number 	(int)
	* @param $order 	(String)
	* @return Boolean
	*/
	public function deleteLimit($user_id, $number, $order = "ASC")
	{
		return $this->notifynderRepository->entity($this->entity)->deleteLimit($user_id, $number, $order);
	}

	/**
	* Add a description to the associate 
	* notification
	*
	* @param $name 	(String)
	* @param $text 	(String)
	* @return \Fenos\Notifynder\Models\NotificationType
	*/
	public function addCategory($name,$text)
	{
		return $this->notifynderCategoryRepository->add($name,$text);
	}

    /**
     * Delete category notification from database
     *
     * @param $id (int)
     * @throws Exceptions\NotificationCategoryNotFoundException
     * @return Boolean
     */
	public function deleteCategory($id = null)
	{
		if ( is_null($id) && !is_null($this->category) ) $id = $this->category;
		
		if (is_null( $id ) && is_null( $this->category ))
		{
			throw new NotificationCategoryNotFoundException("Notification Category Not Found please provide one");
		}

		return $this->notifynderCategoryRepository->delete($id);
	}

    /**
     * Update current category notification
     *
     * @param array $informations (Array)
     * @param $category_id (int)
     * @throws Exceptions\NotificationCategoryNotFoundException
     * @return \Fenos\Notifynder\Models\NotificationType
     */
	public function updateCategory(array $informations,$category_id = null)
	{	
		if ( is_null($category_id) && !is_null($this->category) ) $category_id = $this->category;
		
		if (is_null( $category_id ) && is_null( $this->category ))
		{
			throw new NotificationCategoryNotFoundException("Notification Category Not Found please provide one");
		}

		return $this->notifynderCategoryRepository->update($informations,$category_id);
	}

	/**
	* Translate a category notification
	* Giving the language and the name of it
	*
	* @param $language (String)
	* @param $name
	* @return (String)
	*/
	public function translate($language,$name)
	{
		return $this->notifynderTranslator->translate($language,$name);
	}

	/**
	* Load all the listener availables
	*
	* @param $listen 	(Array)
	* @return Array
	*/
	public function listen(array $listen)
	{
		return $this->notifynderHandler->listen($listen);
	}

    /**
     * Fire the method with the logic of your notification
     * It will execute the closure just if the method fired
     * Will not return false
     *
     * @param $key (String)
     * @param $values (Array)
     * @return \Fenos\Notifynder\Handler\Closure
     */
	public function fire($key, array $values)
	{
		return $this->notifynderHandler->fire($this, $key, $values);
	}

	/**
	* Return the category id
	*
	* @return Category id (int)
	*/
	public function id()
	{
		return $this->category;
	}
}
