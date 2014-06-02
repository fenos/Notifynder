<?php namespace Fenos\Notifynder\Repositories\EloquentRepository;

use Fenos\Notifynder\Models\Notification;
use Illuminate\Database\DatabaseManager as DB;
use Fenos\Notifynder\Parse\NotifynderParse;

//Exceptions
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;

/**
*
*/
class NotifynderRepository implements NotifynderEloquentRepositoryInterface
{
    /**
    * @var \Fenos\Notifynder\Models\Notification (Eloquent)
    */
    protected $notificationModel;

    /**
     * @var $app
     */
    protected $app;

    /**
    * @var Application
    */
    public $db;

    /**
     * @var $entity
     */
    protected $entity;

    /**
     * @param Notification $notificationModel
     * @param DB $db
     */
    function __construct(Notification $notificationModel, DB $db)
    {
        $this->notificationModel = $notificationModel;
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * @param $entity
     * @return $this
     */
    public function entity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Find Notification by id
     *
     * @param $notification_id (int)
     * @throws \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     * @return \Fenos\Notifynder\Models\Notification
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
    * @param $notificationInformations     (Array)
    * @return \Fenos\Notifynder\Models\Notification
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
    * @param $multiNotifications     (Array)
    * @return Boolean
    */
    public function sendMultiple(array $multiNotifications)
    {
        return $this->db->table('notifications')->insert($multiNotifications);
    }

    /**
    * Make read one notification
    *
    * @param $notification_id    (int)
    * @return \Fenos\Notifynder\Models\Notification | False
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
    * @param $to_id     (int)
    * @param $numbers    (int)
    * @param $order     (String) | ASC - DESC
    */
    public function readLimit($to_id,$numbers, $order = "ASC")
    {
        return $this->db->table('notifications')
                        ->where('to_id','=',$to_id)
                        ->orderBy('id',$order)
                        ->limit($numbers)
                        ->update(['read' => 1]);
    }

    /**
    * Make read all notification not read
    *
    * @param $to_id     (int)
    * @return Number of notifications read (int)
    */
    public function readAll($to_id)
    {
        return $this->db->table('notifications')->where('to_id','=',$to_id)->update(['read' => 1]);
    }

    /**
     * Delete a notification giving the id
     * of it
     *
     * @param $notification_id
     * @internal param $id (int)
     * @return Boolean
     */
    public function delete($notification_id)
    {

        $notification = $this->notificationModel->find($notification_id);

        if ( !is_null( $notification) )
        {
            return $notification->delete();
        }

        return false;

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
        return $this->db->table('notifications')->where('to_id',$user_id)->delete();
    }

    /**
    * Delete numbers of notifications equals
    * to the number passing as 2 parameter of
    * the current user
    *
    * @param $user_id     (int)
    * @param $number     (int)
    * @param $order     (String)
    * @return Boolean
    */
    public function deleteLimit($user_id, $number, $order)
    {
        $notifications_id = $this->notificationModel->where('to_id',$user_id)
                            ->orderBy('id',$order)
                            ->select('id')
                            ->limit($number)->get();

        if ( $notifications_id->count() == 0 ) return false;

        $makeItArray = $notifications_id->toArray();
        $array_id = array_flatten($makeItArray);

        return $this->notificationModel->whereIn('id',$array_id)->delete();
    }

    /**
    * Retrive notifications not Read
    * You can also limit the number of
    * Notification if you don't it will get all
    *
    * @param $to_id     (int)
    * @param $limit     (int)
    * @param $paginate    (Boolean)
    * @return \Fenos\Notifynder\Models\Notification Collection
    */
    public function getNotRead($to_id, $limit, $paginate)
    {
        if ( is_null($limit) )
        {
            $result = $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->where('read',0)
                        ->orderBy('created_at','DESC')
                        ->get();

            return $result->parse(); // parse results
        }

        if ($paginate)

            $result = $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->where('read',0)
                        ->orderBy('created_at','DESC')
                        ->paginate($limit);
        else
            $result = $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->where('read',0)
                        ->orderBy('created_at','DESC')
                        ->limit($limit)
                        ->get();

        return $result->parse(); // parse results
    }

    /**
    * Retrive all notifications, not read
    * in first.
    * You can also limit the number of
    * Notifications if you don't, it will get all
    *
    * @param $to_id     (int)
    * @param $limit     (int)
    * @param $paginate    (Boolean)
    * @return \Fenos\Notifynder\Models\Notification Collection
    */
    public function getAll($to_id,$limit = null, $paginate = false)
    {
        if ( is_null($limit) )
        {
            return $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->orderBy('created_at','DESC')
                        ->orderBy('read','ASC')
                        ->get()->parse();
        }

        if ($paginate)
            return $this->notificationModel->with('body','from')
                    ->where('to_id',$to_id)
                    ->orderBy('created_at','DESC')
                    ->orderBy('read','ASC')
                    ->paginate($limit)->parse();
        else
            return $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->orderBy('created_at','DESC')
                        ->orderBy('read','ASC')
                        ->limit($limit)
                        ->get()->parse();
    }

    /**
     * get number Notifications
     * not read
     *
     * @param $to_id
     * @return mixed
     */
    public function countNotRead($to_id)
    {
        return $this->notificationModel->where('to_id',$to_id)
                    ->where('read',0)
                    ->select($this->db->raw('Count(*) as notRead'))
                    ->first();
    }
}
