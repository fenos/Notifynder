<?php namespace Fenos\Notifynder\Repositories\EloquentRepository;

use Fenos\Notifynder\Models\Notification;
use Illuminate\Database\DatabaseManager as DB;
use Fenos\Notifynder\Parse\NotifynderParse;

//Exceptions
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;
use Fenos\Notifynder\Exceptions\EntityNotSpecifiedException;

/**
* Polymorphic repository
*/
class NotifynderRepositoryPolymorphic extends NotifynderRepository implements NotifynderEloquentRepositoryInterface
{
    /**
    * @var \Fenos\Notifynder\Models\Notification (Eloquent)
    */
    protected $notificationModel;

    /**
    * @var $entity
    */
    protected $entity;

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
     * Getter for entity property
     * @param $entity
     * @return $this
     */
    public function entity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * This method check if the entity inserted
     * is not null otherwise it throw an exception
     * because on polymorphic relations is required
     *
     * @throws \Fenos\Notifynder\Exceptions\EntityNotSpecifiedException
     * @return void
     */
    public function entityRequired()
    {
        if (is_null( $this->entity ) )
        {
            throw new EntityNotSpecifiedException("You have to implement the method [entity()] when polymorphic is active");

        }
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
        $this->entityRequired();

        return $this->db->table('notifications')
                        ->where('to_id','=',$to_id)
                        ->where('to_type',$this->entity)
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
        $this->entityRequired();

        return $this->db->table('notifications')->where('to_id','=',$to_id)->where('to_type',$this->entity)->update(['read' => 1]);
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
        $this->entityRequired();

        return $this->db->table('notifications')->where('to_id',$user_id)->where('to_type',$this->entity)->delete();
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
        $this->entityRequired();

        $notifications_id = $this->notificationModel->where('to_id',$user_id)
                            ->where('to_type',$this->entity)
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
        $this->entityRequired();

        if ( is_null($limit) )
        {
            $result = $this->notificationModel->with('body','from')
                        ->where('to_type',$this->entity)
                        ->where('to_id',$to_id)
                        ->where('read',0)
                        ->orderBy('created_at','DESC')
                        ->get();

            return $result->parse(); // parse results
        }

        if ($paginate)

            $result = $this->notificationModel->with('body','from')
                        ->where('to_type',$this->entity)
                        ->where('to_id',$to_id)
                        ->where('read',0)
                        ->orderBy('created_at','DESC')
                    ->paginate($limit);
        else
            $result = $this->notificationModel->with('body','from')
                        ->where('to_type',$this->entity)
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
        $this->entityRequired();

        if ( is_null($limit) )
        {
            return $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->where('to_type',$this->entity)
                        ->orderBy('created_at','DESC')
                        ->orderBy('read','ASC')
                        ->get()->parse();
        }

        if ($paginate)
            return $this->notificationModel->with('body','from')
                    ->where('to_id',$to_id)
                    ->where('to_type',$this->entity)
                    ->orderBy('created_at','DESC')
                    ->orderBy('read','ASC')
                    ->paginate($limit)->parse();
        else
            return $this->notificationModel->with('body','from')
                        ->where('to_id',$to_id)
                        ->where('to_type',$this->entity)
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
            ->where('to_type',$this->entity)
            ->select($this->db->raw('Count(*) as notRead'))
            ->first();
    }
}
