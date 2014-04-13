<?php namespace Fenos\Notifynder\Repositories\EloquentRepository;

/**
* 
*/
interface NotifynderEloquentRepositoryInterface
{

	/**
	* Find Notification by id
	*
	* @param $notification_id	(int)
	* @return Fenos\Notyfinder\Models\Notification | NotificationNotFoundException
	*/
	public function find($notification_id);

	/**
	* Send the notification to the current
	* User
	*
	* @param $from_id 	(int)
	* @param $to_id 	(int)
	* @param $type_id 	(int)
	* @param $url 		(String)
	* @return Bool
	*/
	public function sendOne(array $notificationInformation);

	/**
	* Send Multiple notification at once
	* Remember to add manually the dateTime
	* Because the method Insert of DB doesn't
	* automatcally
	*
	* @param $multiNotifications 	(Array)
	* @return Boolean
	*/
	public function sendMultiple(array $multiNotifications);

	/**
	* Make read one notification
	*
	* @param $notification_id	(int)
	* @return Fenos\Notyfinder\Models\Notification | False
	*/
	public function readOne($notification_id);

	/**
	* Read notifications in base the number
	* Given 
	*
	* @param $to_id 	(int)
	* @param $numbers	(int)
	* @param $position 	(String) | ASC - DESC
	*/
	public function readLimit($to_id,$numbers, $position = "ASC");

	/**
	* Make read all notification not read
	*
	* @param $to_id 	(int)
	* @return Number of notifications read (int)
	*/
	public function readAll($to_id);

	/**
	* Retrive all notifications not read
	* in first.
	* You can also limit the number of
	* Notification if you don't it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return Fenos\Notyfinder\Models\Notification Collection
	*/
	public function getAll($to_id,$limit = null, $paginate = false);

}