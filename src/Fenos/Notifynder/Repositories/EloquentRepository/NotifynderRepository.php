<?php namespace Fenos\Notifynder\Repositories\EloquentRepository;

use Fenos\Notifynder\Models\Notification;
use Illuminate\Database\DatabaseManager as DB;

//Exceptions
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;

/**
* 
*/
class NotifynderRepository implements NotifynderEloquentRepositoryInterface
{
	/**
	* @var instance of Fenos\Notyfinder\Models\Notification (Eloquent)
	*/
	protected $notificationModel;

	/**
	* @var Application
	*/
	public $db;

	function __construct(Notification $notificationModel, DB $db)
	{
		$this->notificationModel = $notificationModel;
		$this->db = $db;
	}

	public function app()
	{
		return $this->app;
	}

	/**
	* Find Notification by id
	*
	* @param $notification_id	(int)
	* @return Fenos\Notyfinder\Models\Notification | NotificationNotFoundException
	*/
	public function find($notification_id)
	{
		$notification = $this->notificationModel->find($notification_id);

		if ( !is_null($notification) )
		{
			return $notification;
		}

		throw new NotificationNotFoundException("Notification Not Found");

	}

	/**
	* Send the notification to the current
	* User 
	*
	* @param $notificationInformations 	(Array)
	* @return Fenos\Notyfinder\Models\Notification
	*/
	public function sendOne(array $notificationInformations)
	{
		return $this->notificationModel->create($notificationInformations);
	}

	/**
	* Send Multiple notification at once
	* Remember to add manually the dateTime
	* Because the method Insert of DB doesn't
	* automatcally
	*
	* @param $multiNotifications 	(Array)
	* @return Boolean
	*/
	public function sendMultiple(array $multiNotifications)
	{
		return $this->db->table('notifications')->insert($multiNotifications);
	}

	/**
	* Make read one notification
	*
	* @param $notification_id	(int)
	* @return Fenos\Notyfinder\Models\Notification | False
	*/
	public function readOne($notification_id)
	{
		// find notification
		$notification = $this->notificationModel->find($notification_id);

		if ( !is_null($notification) )
		{

			// update notification
			$notification->read = 1;
			$notification->save();

			return $notification;

		}
		
		return false;
		
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
		return $this->db->table('notifications')
						->where('to_id','=',$to_id)
						->orderBy('id',$position)
						->limit($numbers)
						->update(['read' => 1]);
	}

	/**
	* Make read all notification not read
	*
	* @param $to_id 	(int)
	* @return Number of notifications read (int)
	*/
	public function readAll($to_id)
	{
		return $this->db->table('notifications')->where('to_id','=',$to_id)->update(['read' => 1]);
	}

	/**
	* Retrive notifications not Read
	* You can also limit the number of
	* Notification if you don't it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return Fenos\Notyfinder\Models\Notification Collection
	*/
	public function getNotRead($to_id, $limit, $paginate)
	{
		if ( is_null($limit) )
		{
			return $this->notificationModel->with('body','user')
						->where('to_id',$to_id)
						->where('read',0)
						->orderBy('created_at','DESC')
						->get();
		}

		if ($paginate)

			return $this->notificationModel->with('body','user')
					->where('to_id',$to_id)
					->where('read',0)
					->orderBy('created_at','DESC')
					->paginate($limit);
		else
			return $this->notificationModel->with('body','user')
						->where('to_id',$to_id)
						->where('read',0)
						->orderBy('created_at','DESC')
						->limit($limit)
						->get();
	}

	/**
	* Retrive all notifications, not read
	* in first.
	* You can also limit the number of
	* Notification if you don't it will get all
	*
	* @param $to_id 	(int)
	* @param $limit 	(int)
	* @param $paginate	(Boolean)
	* @return Fenos\Notyfinder\Models\Notification Collection
	*/
	public function getAll($to_id,$limit = null, $paginate = false)
	{
		if ( is_null($limit) )
		{
			return $this->notificationModel->with('body','user')
						->where('to_id',$to_id)
						->orderBy('created_at','DESC')
						->orderBy('read','ASC')
						->get();
		}

		if ($paginate)
			return $this->notificationModel->with('body','user')
					->where('to_id',$to_id)
					->orderBy('created_at','DESC')
					->orderBy('read','ASC')
					->paginate($limit);
		else
			return $this->notificationModel->with('body','user')
						->where('to_id',$to_id)
						->orderBy('created_at','DESC')
						->orderBy('read','ASC')
						->limit($limit)
						->get();
	}
}