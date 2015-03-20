<?php namespace Fenos\Notifynder\Notifications;

use Fenos\Notifynder\Exceptions\NotificationNotFoundException;
use Fenos\Notifynder\Notifications\Repositories\NotificationRepository;
use Fenos\Notifynder\Senders\StoreNotification;
use Illuminate\Support\Facades\Paginator;

/**
 * Class NotifynderNotification
 *
 * @package Fenos\Notifynder\Notifications
 */
class NotifynderNotification implements StoreNotification
{

    /**
     * @var NotificationRepository
     */
    protected $notifynderRepo;

    /**
     * @var string | null
     */
    protected $entity;

    /**
     * @param NotificationRepository $notifynderRepo
     */
    public function __construct(NotificationRepository $notifynderRepo)
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
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     */
    public function find($notification_id)
    {
        $notification = $this->notifynderRepo->find($notification_id);

        if (is_null($notification)) {
            throw new NotificationNotFoundException("Notification Not found");
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
        $notifications = $this->notifynderRepo->entity($this->entity);

        return $notifications->readLimit($to_id, $numbers, $order);
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
        $notifications = $this->notifynderRepo->entity($this->entity);

        return $notifications->readAll($to_id);
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
    public function deleteLimit($entity_id, $number, $order)
    {
        $notifications = $this->notifynderRepo->entity($this->entity);

        return $notifications->deleteLimit($entity_id, $number, $order);
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
        $notifications = $this->notifynderRepo->entity($this->entity);

        return $notifications->deleteAll($entity_id);
    }

    /**
     * Get notifications not read
     * of the entity given
     *
     * @param $to_id
     * @param $limit
     * @param $paginate
     * @return mixed
     */
    public function getNotRead($to_id, $limit, $paginate)
    {
        $notifications = $this->notifynderRepo->getNotRead(
            $to_id, $this->entity,
            $limit, $paginate
        );

        if ($paginate) {
            $offset = (Paginator::getCurrentPage() * $limit) - $limit;
            $items = array_slice($notifications->parse()->getCollectionItems(), $offset, $limit);

            return Paginator::make(
                 $items, count($notifications->parse()), $limit
            );
        }

        return $notifications->parse();
    }

    /**
     * Get All notifications
     *
     * @param $to_id
     * @param $limit
     * @param $paginate
     * @return mixed
     */
    public function getAll($to_id, $limit, $paginate)
    {
        $notifications = $this->notifynderRepo->getAll(
            $to_id, $this->entity,
            $limit, $paginate
        );

        if ($paginate) {
            $offset = (Paginator::getCurrentPage() * $limit) - $limit;
            $items = array_slice($notifications->parse()->getCollectionItems(), $offset, $limit);

            return Paginator::make(
                 $items, count($notifications->parse()), $limit
            );
        }

        return $notifications->parse();
    }

    /**
     * Send single notification
     *
     * @param  array  $info
     * @return static
     */
    public function sendOne(array $info)
    {
        return $this->notifynderRepo->sendSingle($info);
    }

    /**
     * Send multiple notifications
     *
     * @param  array $info
     * @return mixed
     */
    public function sendMultiple(array $info)
    {
        return $this->notifynderRepo->sendMultiple($info);
    }

    /**
     * Get number of notification
     * not read
     *
     * @param $to_id
     * @return mixed
     */
    public function countNotRead($to_id)
    {
        $notifications =  $this->notifynderRepo->entity($this->entity);

        return $notifications->countNotRead($to_id);
    }
}
