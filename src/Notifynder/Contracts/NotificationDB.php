<?php

namespace Fenos\Notifynder\Contracts;

use Closure;
use Fenos\Notifynder\Models\Notification;

/**
 * Class NotificationRepository.
 */
interface NotificationDB extends StoreNotification
{
    /**
     * Find notification by id.
     *
     * @param $notificationId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($notificationId);

    /**
     * Make Read One Notification.
     *
     * @param  Notification $notification
     * @return bool|Notification
     */
    public function readOne(Notification $notification);

    /**
     * Read notifications in base the number
     * Given.
     *
     * @param $toId
     * @param $entity
     * @param $numbers
     * @param $order
     * @return int
     */
    public function readLimit($toId, $entity, $numbers, $order);

    /**
     * Make read all notification not read.
     *
     * @param $toId
     * @param $entity
     * @return int
     */
    public function readAll($toId, $entity);

    /**
     * Delete a notification giving the id
     * of it.
     *
     * @param $notificationId
     * @return bool
     */
    public function delete($notificationId);

    /**
     * Delete All notifications about the
     * current user.
     *
     * @param $toId int
     * @param $entity
     * @return bool
     */
    public function deleteAll($toId, $entity);

    /**
     * Delete All notifications from a
     * defined category.
     *
     * @param $categoryName
     * @param $expired Bool
     * @return bool
     */
    public function deleteByCategory($categoryName, $expired = false);

    /**
     * Delete numbers of notifications equals
     * to the number passing as 2 parameter of
     * the current user.
     *
     * @param $userId    int
     * @param $entity
     * @param $number     int
     * @param $order      string
     * @return int
     * @throws \Exception
     */
    public function deleteLimit($userId, $entity, $number, $order);

    /**
     * Retrieve notifications not Read
     * You can also limit the number of
     * Notification if you don't it will get all.
     *
     * @param           $toId
     * @param           $entity
     * @param           $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getNotRead(
        $toId,
        $entity,
        $limit,
        $paginate = null,
        $orderDate = 'desc',
        Closure $filterScope = null
    );

    /**
     * Retrieve all notifications, not read
     * in first.
     * You can also limit the number of
     * Notifications if you don't, it will get all.
     *
     * @param           $toId
     * @param           $entity
     * @param  null     $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getAll(
        $toId,
        $entity,
        $limit = null,
        $paginate = null,
        $orderDate = 'desc',
        Closure $filterScope = null
    );

    /**
     * get number Notifications
     * not read.
     *
     * @param         $toId
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($toId, $entity, Closure $filterScope = null);

    /**
     * Get last notification of the current
     * entity.
     *
     * @param         $toId
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($toId, $entity, Closure $filterScope = null);

    /**
     * Get last notification of the current
     * entity of a specific category.
     *
     * @param         $category
     * @param         $toId
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotificationByCategory($category, $toId, $entity, Closure $filterScope = null);
}
