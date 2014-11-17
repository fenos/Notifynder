<?php namespace Fenos\Notifynder\Notifications\Repositories;

use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Senders\StoreNotification;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderDB;

/**
 * Class NotificationRepository
 *
 * @package Fenos\Notifynder\Senders
 */
class NotificationRepository {

    use PolymorphicRepository;

    /**
     * @var Notification | Builder | BuilderDB
     */
    protected $notification;

    /**
     * @var $db DatabaseManager | Connection
     */
    protected $db;

    /**
     * @param Notification                         $notification
     * @param \Illuminate\Database\DatabaseManager $db
     */
    function __construct(Notification $notification,
                         DatabaseManager $db)
    {
        $this->notification = $notification;
        $this->db = $db;
    }

    /**
     * Find notification by id
     *
     * @param $notification_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($notification_id)
    {
        return $this->notification->find($notification_id);
    }

    /**
     * Save a single notification sent
     *
     * @param array $info
     * @return static
     */
    public function sendSingle(array $info)
    {
        return $this->notification->create($info);
    }

    /**
     * Save multiple notifications sent
     * at once
     *
     * @param array $info
     * @return mixed
     */
    public function sendMultiple(array $info)
    {
        return $this->db->table('notifications')->insert($info);
    }

    /**
     * Make Read One Notification
     *
     * @param Notification $notification
     * @return bool|Notification
     */
    public function readOne(Notification $notification)
    {
        $notification->read = 1;

        if ($notification->save())
        {
            return $notification;
        }

        return false;
    }

    /**
     * Read notifications in base the number
     * Given
     *
     * @param $to_id
     * @param $numbers
     * @param $order
     * @return mixed
     */
    public function readLimit($to_id,$numbers, $order)
    {
        return $this->notification->withNotRead()
            ->limit($numbers)
            ->orderBy('id',$order)
            ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
            ->update(['read' => 1]);
    }

    /**
     * Make read all notification not read
     *
     * @param $to_id
     * @return mixed
     */
    public function readAll($to_id)
    {
        return $this->notification->withNotRead()
            ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
            ->update(['read' => 1]);
    }

    /**
     * Delete a notification giving the id
     * of it
     *
     * @param $notification_id
     * @return Bool
     */
    public function delete($notification_id)
    {
        return $this->notification->where('id',$notification_id)->delete();
    }

    /**
     * Delete All notifications about the
     * current user
     *
     * @param $to_id int
     * @return Bool
     */
    public function deleteAll($to_id)
    {
        $query =  $this->db->table('notifications');

        return $this->wherePolymorphic('to_id','to_type',$to_id,$this->entity,$query)->delete();
    }

    /**
     * Delete numbers of notifications equals
     * to the number passing as 2 parameter of
     * the current user
     *
     * @param $user_id    int
     * @param $number     int
     * @param $order      string
     * @return Bool
     */
    public function deleteLimit($user_id, $number, $order)
    {
        $notifications_id = $this->notification
            ->wherePolymorphic('to_id','to_type',$user_id,$this->entity)
            ->orderBy('id',$order)
            ->select('id')
            ->limit($number)->get();

        if ( $notifications_id->count() == 0 ) return false;

        $makeItArray = $notifications_id->toArray();
        $array_id = array_flatten($makeItArray);

        return $this->notification->whereIn('id',$array_id)->delete();
    }

    /**
     * Retrive notifications not Read
     * You can also limit the number of
     * Notification if you don't it will get all
     *
     * @param $to_id
     * @param $limit
     * @param $paginate
     * @return mixed
     */
    public function getNotRead($to_id, $limit, $paginate)
    {
        if ( is_null($limit) )
        {
            $result = $this->notification->with('body','from')
                ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
                ->withNotRead()
                ->orderBy('read','ASC')
                ->get();
        }

        if ($paginate)
        {
            $result = $this->notification->with('body','from')
                ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
                ->withNotRead()
                ->orderBy('read','ASC')
                ->paginate($limit);
        }
        else
        {
            $result = $this->notification->with('body','from')
                ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
                ->withNotRead()
                ->limit($limit)
                ->orderBy('read','ASC')
                ->get();
        }

        return $result;
    }


    /**
     * Retrive all notifications, not read
     * in first.
     * You can also limit the number of
     * Notifications if you don't, it will get all
     *
     * @param      $to_id
     * @param null $limit
     * @param bool $paginate
     * @return mixed
     */
    public function getAll($to_id,$limit = null, $paginate = false)
    {
        if ( is_null($limit) )
        {
            return $this->notification->with('body','from')
                ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
                ->orderBy('read','ASC')
                ->get();
        }

        if ($paginate)
        {
            return $this->notification->with('body','from')
                ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
                ->orderBy('read','ASC')
                ->paginate($limit);
        }
        else
        {
            return $this->notification->with('body','from')
                ->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
                ->orderBy('read','ASC')
                ->limit($limit)
                ->get();
        }
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
        return $this->notification->wherePolymorphic('to_id','to_type',$to_id,$this->entity)
            ->withNotRead()
            ->select($this->db->raw('Count(*) as notRead'))
            ->first();
    }
} 