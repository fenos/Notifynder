<?php

namespace Fenos\Notifynder\Contracts;

use Closure;
use Fenos\Notifynder\Models\Notification as NotificationModel;

/**
 * Class NotifynderNotification.
 */
interface NotifynderNotification
{
    /**
     * Set the entity for polymorphic.
     *
     * @param $name
     * @return $this
     */
    public function entity($name);

    /**
     * Find a notification by ID.
     *
     * @param $notificationId
     * @return NotificationModel|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     */
    public function find($notificationId);

    /**
     * Make read one notification giving
     * the ID of it.
     *
     * @param $notificationId
     * @return bool|\Fenos\Notifynder\Models\Notification
     */
    public function readOne($notificationId);

    /**
     * Read notifications in base the number
     * Given.
     *
     * @param         $toId
     * @param         $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimit($toId, $numbers, $order = 'ASC');

    /**
     * Read all notification of the
     * given entity.
     *
     * @param $toId
     * @return Number
     */
    public function readAll($toId);

    /**
     * Delete a notification giving the id
     * of it.
     *
     * @param $notificationId
     * @return bool
     */
    public function delete($notificationId);

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
    public function deleteLimit($entityId, $number, $order);

    /**
     * Delete all notification of a given
     * Entity.
     *
     * @param $entityId
     * @return bool
     */
    public function deleteAll($entityId);

    /**
     * Delete All notifications from a
     * defined category.
     *
     * @param $categoryName string
     * @param $expired Bool
     * @return bool
     */
    public function deleteByCategory($categoryName, $expired = false);

    /**
     * Get notifications not read
     * of the entity given.
     *
     * @param         $toId
     * @param         $limit
     * @param         $paginate
     * @param  string $orderDate
     * @param Closure $filterScope
     * @return mixed
     */
    public function getNotRead($toId, $limit, $paginate, $orderDate = 'desc', Closure $filterScope = null);

    /**
     * Get All notifications.
     *
     * @param         $toId
     * @param         $limit
     * @param         $paginate
     * @param  string $orderDate
     * @param Closure $filterScope
     * @return mixed
     */
    public function getAll($toId, $limit, $paginate, $orderDate = 'desc', Closure $filterScope = null);

    /**
     * Get last notification of the
     * given entity.
     *
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($toId, Closure $filterScope = null);

    /**
     * Get last notification of the
     * given entity of the specific category.
     *
     * @param         $category
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotificationByCategory($category, $toId, Closure $filterScope = null);

    /**
     * Send single notification.
     *
     * @param  array  $info
     * @return static
     */
    public function sendOne(array $info);

    /**
     * Send multiple notifications.
     *
     * @param  array $info
     * @return mixed
     */
    public function sendMultiple(array $info);

    /**
     * Get number of notification
     * not read.
     *
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($toId, Closure $filterScope = null);
}
