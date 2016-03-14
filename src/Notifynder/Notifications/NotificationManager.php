<?php namespace Fenos\Notifynder\Notifications;

use Closure;
use Fenos\Notifynder\Contracts\NotificationDB;
use Fenos\Notifynder\Contracts\NotifynderNotification;
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;
use Fenos\Notifynder\Models\Notification as NotificationModel;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class NotifynderNotification
 *
 * The notification manager is responsable to manage the CRUD operations
 * of the notifications.
 *
 * @package Fenos\Notifynder\Notifications
 */
class NotificationManager implements NotifynderNotification
{

    /**
     * @var NotificationDB
     */
    protected $notifynderRepo;

    /**
     * @var string | null
     */
    protected $entity;

    /**
     * @param NotificationDB $notifynderRepo
     */
    public function __construct(NotificationDB $notifynderRepo)
    {
        $this->notifynderRepo = $notifynderRepo;
    }

    /**
     * Set the entity for polymorphic
     *
     * @param $name
     * @return $this
     */
    public function entity($name)
    {
        $this->entity = $name;

        return $this;
    }

    /**
     * Find a notification by ID
     *
     * @param $notification_id
     * @return NotificationModel|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     */
    public function find($notification_id)
    {
        $notification = $this->notifynderRepo->find($notification_id);

        if (is_null($notification)) {
            $error = "Notification Not found";
            throw new NotificationNotFoundException($error);
        }

        return $notification;
    }

    /**
     * Make read one notification giving
     * the ID of it
     *
     * @param $notification_id
     * @return bool|\Fenos\Notifynder\Models\Notification
     */
    public function readOne($notification_id)
    {
        $notification = $this->find($notification_id);

        return $this->notifynderRepo->readOne($notification);
    }

    /**
     * Read notifications in base the number
     * Given
     *
     * @param         $to_id
     * @param         $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimit($to_id, $numbers, $order = "ASC")
    {
        return $this->notifynderRepo->readLimit($to_id, $this->entity, $numbers, $order);
    }

    /**
     * Read all notification of the
     * given entity
     *
     * @param $to_id
     * @return Number
     */
    public function readAll($to_id)
    {
        return $this->notifynderRepo->readAll($to_id, $this->entity);
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
        return $this->notifynderRepo->delete($notification_id);
    }

    /**
     * Delete numbers of notifications equals
     * to the number passing as 2 parameter of
     * the current user
     *
     * @param $entity_id
     * @param $number
     * @param $order
     * @return mixed
     */
    public function deleteLimit($entity_id, $number, $order = 'asc')
    {
        return $this->notifynderRepo->deleteLimit($entity_id, $this->entity, $number, $order);
    }

    /**
     * Delete all notification of a given
     * Entity
     *
     * @param $entity_id
     * @return Bool
     */
    public function deleteAll($entity_id)
    {
        return $this->notifynderRepo->deleteAll($entity_id, $this->entity);
    }

    /**
     * Delete All notifications from a
     * defined category
     *
     * @param $category_name string
     * @param $expired Bool
     * @return Bool
     */
    public function deleteByCategory($category_name, $expired = false)
    {
        return $this->notifynderRepo->deleteByCategory($category_name, $expired);
    }

    /**
     * Get notifications not read
     * of the entity given
     *
     * @param           $to_id
     * @param           $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getNotRead($to_id, $limit = null, $paginate = null, $orderDate = 'desc', Closure $filterScope = null)
    {
        $notifications = $this->notifynderRepo->getNotRead(
            $to_id, $this->entity,
            $limit, $paginate, $orderDate,
            $filterScope
        );

        if(is_int(intval($paginate)) && $paginate)
        {
            return (new LengthAwarePaginator($notifications->parse(), $notifications->total(), $limit, $paginate, [
                'path' => LengthAwarePaginator::resolveCurrentPath()
            ]));
        }

        return $notifications->parse();
    }

    /**
     * Get All notifications
     *
     * @param           $to_id
     * @param           $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getAll($to_id, $limit = null, $paginate = null, $orderDate = 'desc', Closure $filterScope = null)
    {
        $notifications = $this->notifynderRepo->getAll(
            $to_id, $this->entity,
            $limit, $paginate, $orderDate,
            $filterScope
        );

        if(is_int(intval($paginate)) && $paginate)
        {
            return (new LengthAwarePaginator($notifications->parse(), $notifications->total(), $limit, $paginate, [
                'path' => LengthAwarePaginator::resolveCurrentPath()
            ]));
        }

        return $notifications->parse();
    }

    /**
     * Get last notification of the
     * given entity
     *
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($to_id, Closure $filterScope = null)
    {
        return $this->notifynderRepo->getLastNotification($to_id,$this->entity,$filterScope);
    }

    /**
     * Get last notification of the
     * given entity of the specific category
     *
     * @param         $category
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotificationByCategory($category,$to_id, Closure $filterScope = null)
    {
        return $this->notifynderRepo->getLastNotificationByCategory($category,$to_id,$this->entity,$filterScope);
    }

    /**
     * Send single notification
     *
     * @param  array  $info
     * @return static
     */
    public function sendOne(array $info)
    {
        return $this->notifynderRepo->storeSingle($info);
    }

    /**
     * Send multiple notifications
     *
     * @param  array $info
     * @return mixed
     */
    public function sendMultiple(array $info)
    {
        return $this->notifynderRepo->storeMultiple($info);
    }

    /**
     * Get number of notification
     * not read
     *
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($to_id, Closure $filterScope = null)
    {
        return $this->notifynderRepo->countNotRead($to_id, $this->entity);
    }
}
