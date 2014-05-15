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
	* @return \Fenos\Notifynder\Models\Notification | NotificationNotFoundException
	*/
	public function find($notification_id);

	/**
	* Send the notification to the current
	* User 
	*
	* @param $notificationInformations 	(Array)
	* @return \Fenos\Notifynder\Models\Notification
	*/
	public function sendOne(array $notificationInformations);

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
	* @return \Fenos\Notifynder\Models\Notification | False
	*/
	public function readOne($notification_id);

	/**
	* Read notifications in base the number
	* Given 
	*
	* @param $to_id 	(int)
	* @param $numbers	(int)
	* @param $order 	(String) | ASC - DESC
	*/
	public function readLimit($to_id,$numbers, $order = "ASC");

	/**
	* Make read all notification not read
	*
	* @param $to_id 	(int)
	* @return Number of notifications read (int)
	*/
	public function readAll($to_id);

	/**
	* Delete a notification giving the id
	* of it
	*
	* @param $id (int)
	* @return Boolean
	*/
	public function delete($notification_id);

	/**
	* Delete All notifications about the
	* current user 
	*
	* @param $user_id (int)
	* @return Boolean
	*/
	public function deleteAll($user_id);

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
	public function deleteLimit($user_id, $number, $order);

	/**
	* Retrive notifications not Read
	* You can also limit the number of
	* Notification if you don't it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return \Fenos\Notifynder\Models\Notification Collection
	*/
	public function getNotRead($to_id, $limit, $paginate);

	/**
	* Retrive all notifications, not read
	* in first.
	* You can also limit the number of
	* Notifications if you don't, it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return \Fenos\Notifynder\Models\Notification Collection
	*/
	public function getAll($to_id,$limit = null, $paginate = false);

}