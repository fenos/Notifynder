<?php

namespace Fenos\Notifynder\Notifications;

use Closure;
use Fenos\Notifynder\Contracts\NotificationDB;
use Fenos\Notifynder\Contracts\NotifynderNotification;
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;
use Fenos\Notifynder\Models\Notification as NotificationModel;
use Fenos\Notifynder\Models\NotifynderCollection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class NotifynderNotification.
 *
 * The notification manager is responsible to manage the CRUD operations
 * of the notifications.
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
     * Set the entity for polymorphic.
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
     * Find a notification by ID.
     *
     * @param $notificationId
     * @return NotificationModel|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     */
    public function find($notificationId)
    {
        $notification = $this->notifynderRepo->find($notificationId);

        if (is_null($notification)) {
            $error = 'Notification Not found';
            throw new NotificationNotFoundException($error);
        }

        return $notification;
    }

    /**
     * Make read one notification giving
     * the ID of it.
     *
     * @param $notificationId
     * @return bool|\Fenos\Notifynder\Models\Notification
     */
    public function readOne($notificationId)
    {
        $notification = $this->find($notificationId);

        return $this->notifynderRepo->readOne($notification);
    }

    /**
     * Read notifications in base the number
     * Given.
     *
     * @param         $toId
     * @param         $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimit($toId, $numbers, $order = 'ASC')
    {
        return $this->notifynderRepo->readLimit($toId, $this->entity, $numbers, $order);
    }

    /**
     * Read all notification of the
     * given entity.
     *
     * @param $toId
     * @return Number
     */
    public function readAll($toId)
    {
        return $this->notifynderRepo->readAll($toId, $this->entity);
    }

    /**
     * Delete a notification giving the id
     * of it.
     *
     * @param $notificationId
     * @return bool
     */
    public function delete($notificationId)
    {
        return $this->notifynderRepo->delete($notificationId);
    }

    /**
     * Delete numbers of notifications equals
     * to the number passing as 2 parameter of
     * the current user.
     *
     * @param $entityId
     * @param $number
     * @param $order
     * @return mixed
     */
    public function deleteLimit($entityId, $number, $order = 'asc')
    {
        return $this->notifynderRepo->deleteLimit($entityId, $this->entity, $number, $order);
    }

    /**
     * Delete all notification of a given
     * Entity.
     *
     * @param $entityId
     * @return bool
     */
    public function deleteAll($entityId)
    {
        return $this->notifynderRepo->deleteAll($entityId, $this->entity);
    }

    /**
     * Delete All notifications from a
     * defined category.
     *
     * @param $categoryName string
     * @param $expired Bool
     * @return bool
     */
    public function deleteByCategory($categoryName, $expired = false)
    {
        return $this->notifynderRepo->deleteByCategory($categoryName, $expired);
    }

    /**
     * Get notifications not read
     * of the entity given.
     *
     * @param           $toId
     * @param           $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getNotRead($toId, $limit = null, $paginate = null, $orderDate = 'desc', Closure $filterScope = null)
    {
        $queryLimit = $limit;
        if ($this->isPaginated($paginate)) {
            $queryLimit = null;
        }

        $notifications = $this->notifynderRepo->getNotRead(
            $toId, $this->entity,
            $queryLimit, null, $orderDate,
            $filterScope
        );

        return $this->getPaginatedIfNeeded($notifications, $limit, $paginate);
    }

    /**
     * Get All notifications.
     *
     * @param           $toId
     * @param           $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getAll($toId, $limit = null, $paginate = null, $orderDate = 'desc', Closure $filterScope = null)
    {
        $queryLimit = $limit;
        if ($this->isPaginated($paginate)) {
            $queryLimit = null;
        }

        $notifications = $this->notifynderRepo->getAll(
            $toId, $this->entity,
            $queryLimit, null, $orderDate,
            $filterScope
        );

        return $this->getPaginatedIfNeeded($notifications, $limit, $paginate);
    }

    protected function isPaginated($paginate)
    {
        return ! ($paginate === false || is_null($paginate));
    }

    protected function getPaginatedIfNeeded(NotifynderCollection $notifications, $perPage, $paginate)
    {
        if (! $this->isPaginated($paginate)) {
            return $notifications->parse();
        } elseif ($paginate === true) {
            $paginate = null;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $notifications->count();
        $notifications = $notifications->forPage($page, $perPage);

        return new LengthAwarePaginator($notifications->parse(), $total, $perPage, $paginate, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
    }

    /**
     * Get last notification of the
     * given entity.
     *
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($toId, Closure $filterScope = null)
    {
        return $this->notifynderRepo->getLastNotification($toId, $this->entity, $filterScope);
    }

    /**
     * Get last notification of the
     * given entity of the specific category.
     *
     * @param         $category
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotificationByCategory($category, $toId, Closure $filterScope = null)
    {
        return $this->notifynderRepo->getLastNotificationByCategory($category, $toId, $this->entity, $filterScope);
    }

    /**
     * Send single notification.
     *
     * @param  array  $info
     * @return static
     */
    public function sendOne(array $info)
    {
        return $this->notifynderRepo->storeSingle($info);
    }

    /**
     * Send multiple notifications.
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
     * not read.
     *
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($toId, Closure $filterScope = null)
    {
        return $this->notifynderRepo->countNotRead($toId, $this->entity, $filterScope);
    }
}
