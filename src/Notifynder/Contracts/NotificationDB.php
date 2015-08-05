<?php namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Models\Notification;

/**
 * Class NotificationRepository
 *
 * @package Fenos\Notifynder\Senders
 */
interface NotificationDB
{

    /**
     * Find notification by id
     *
     * @param $notification_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($notification_id);

    /**
     * Save a single notification sent
     *
     * @param  array  $info
     * @return static
     */
    public function storeSingle(array $info);

    /**
     * Save multiple notifications sent
     * at once
     *
     * @param  array $info
     * @return mixed
     */
    public function storeMultiple(array $info);

    /**
     * Make Read One Notification
     *
     * @param  Notification      $notification
     * @return bool|Notification
     */
    public function readOne(Notification $notification);

    /**
     * Read notifications in base the number
     * Given
     *
     * @param $to_id
     * @param $entity
     * @param $numbers
     * @param $order
     * @return int
     */
    public function readLimit($to_id, $entity, $numbers, $order);

    /**
     * Make read all notification not read
     *
     * @param $to_id
     * @param $entity
     * @return int
     */
    public function readAll($to_id, $entity);

    /**
     * Delete a notification giving the id
     * of it
     *
     * @param $notification_id
     * @return Bool
     */
    public function delete($notification_id);

    /**
     * Delete All notifications about the
     * current user
     *
     * @param $to_id int
     * @param $entity
     * @return Bool
     */
    public function deleteAll($to_id, $entity);

    /**
     * Delete All notifications from a
     * defined category
     *
     * @param $category_name
     * @param $expired Bool
     * @return Bool
     */
    public function deleteByCategory($category_name, $expired = false);

    /**
     * Delete numbers of notifications equals
     * to the number passing as 2 parameter of
     * the current user
     *
     * @param $user_id    int
     * @param $entity
     * @param $number     int
     * @param $order      string
     * @return int
     * @throws \Exception
     */
    public function deleteLimit($user_id, $entity, $number, $order);

    /**
     * Retrive notifications not Read
     * You can also limit the number of
     * Notification if you don't it will get all
     *
     * @param         $to_id
     * @param         $entity
     * @param         $limit
     * @param  int|null $paginate
     * @param  string $orderDate
     * @return mixed
     */
    public function getNotRead($to_id, $entity, $limit, $paginate = null, $orderDate = 'desc');

    /**
     * Retrive all notifications, not read
     * in first.
     * You can also limit the number of
     * Notifications if you don't, it will get all
     *
     * @param         $to_id
     * @param         $entity
     * @param  null   $limit
     * @param  int|null $paginate
     * @param  string $orderDate
     * @return mixed
     */
    public function getAll($to_id, $entity, $limit = null, $paginate = null, $orderDate = 'desc');

    /**
     * get number Notifications
     * not read
     *
     * @param $to_id
     * @param $entity
     * @return mixed
     */
    public function countNotRead($to_id, $entity);

    /**
     * Get last notification of the current
     * entity
     *
     * @param $to_id
     * @param $entity
     * @return mixed
     */
    public function getLastNotification($to_id,$entity);

    /**
     * Get last notification of the current
     * entity of a specific category
     *
     * @param $category
     * @param $to_id
     * @param $entity
     * @return mixed
     */
    public function getLastNotificationByCategory($category,$to_id,$entity);
}
