<?php namespace Fenos\Notifynder\Contracts;

use Closure;
use Fenos\Notifynder\Models\Notification as NotificationModel;

/**
 * Class NotifynderNotification
 *
 * @package Fenos\Notifynder\Notifications
 */
interface NotifynderNotification
{

    /**
     * Set the entity for polymorphic
     *
     * @param $name
     * @return $this
     */
    public function entity($name);

    /**
     * Find a notification by ID
     *
     * @param $notification_id
     * @return NotificationModel|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     */
    public function find($notification_id);

    /**
     * Make read one notification giving
     * the ID of it
     *
     * @param $notification_id
     * @return bool|\Fenos\Notifynder\Models\Notification
     */
    public function readOne($notification_id);

    /**
     * Read notifications in base the number
     * Given
     *
     * @param         $to_id
     * @param         $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimit($to_id, $numbers, $order = "ASC");

    /**
     * Read all notification of the
     * given entity
     *
     * @param $to_id
     * @return Number
     */
    public function readAll($to_id);

    /**
     * Delete a notification giving the id
     * of it
     *
     * @param $notification_id
     * @return Bool
     */
    public function delete($notification_id);

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
    public function deleteLimit($entity_id, $number, $order);

    /**
     * Delete all notification of a given
     * Entity
     *
     * @param $entity_id
     * @return Bool
     */
    public function deleteAll($entity_id);

    /**
     * Delete All notifications from a
     * defined category
     *
     * @param $category_name string
     * @param $expired Bool
     * @return Bool
     */
    public function deleteByCategory($category_name, $expired = false);

    /**
     * Get notifications not read
     * of the entity given
     *
     * @param         $to_id
     * @param         $limit
     * @param         $paginate
     * @param  string $orderDate
     * @param Closure $filterScope
     * @return mixed
     */
    public function getNotRead($to_id, $limit, $paginate, $orderDate = "desc", Closure $filterScope = null);

    /**
     * Get All notifications
     *
     * @param         $to_id
     * @param         $limit
     * @param         $paginate
     * @param  string $orderDate
     * @param Closure $filterScope
     * @return mixed
     */
    public function getAll($to_id, $limit, $paginate, $orderDate = "desc",  Closure $filterScope = null);

    /**
     * Get last notification of the
     * given entity
     *
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($to_id,  Closure $filterScope = null);

    /**
     * Get last notification of the
     * given entity of the specific category
     *
     * @param         $category
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotificationByCategory($category,$to_id,  Closure $filterScope = null);

    /**
     * Send single notification
     *
     * @param  array  $info
     * @return static
     */
    public function sendOne(array $info);

    /**
     * Send multiple notifications
     *
     * @param  array $info
     * @return mixed
     */
    public function sendMultiple(array $info);

    /**
     * Get number of notification
     * not read
     *
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($to_id,  Closure $filterScope = null);
}
