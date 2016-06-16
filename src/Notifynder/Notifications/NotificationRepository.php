<?php

namespace Fenos\Notifynder\Notifications;

use Closure;
use Fenos\Notifynder\Contracts\NotificationDB;
use Fenos\Notifynder\Models\Notification;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderDB;

/**
 * Class NotificationRepository.
 */
class NotificationRepository implements NotificationDB
{
    /**
     * @var Notification | Builder | BuilderDB
     */
    protected $notification;

    /**
     * @var DatabaseManager | Connection
     */
    protected $db;

    /**
     * @param Notification                         $notification
     * @param \Illuminate\Database\DatabaseManager $db
     */
    public function __construct(
        Notification $notification,
        DatabaseManager $db
    ) {
        $this->notification = $notification;
        $this->db = $db;
    }

    /**
     * Find notification by id.
     *
     * @param $notificationId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($notificationId)
    {
        return $this->notification->find($notificationId);
    }

    /**
     * Save a single notification sent.
     *
     * @param  array $info
     * @return Notification
     */
    public function storeSingle(array $info)
    {
        return $this->notification->create($info);
    }

    /**
     * Save multiple notifications sent
     * at once.
     *
     * @param  array $notifications
     * @return mixed
     */
    public function storeMultiple(array $notifications)
    {
        $this->db->beginTransaction();
        $stackId = $this->db->table(
            $this->notification->getTable()
        )->max('stack_id') + 1;
        foreach ($notifications as $key => $notification) {
            $notifications[$key]['stack_id'] = $stackId;
        }
        $insert = $this->db->table(
            $this->notification->getTable()
        )->insert($notifications);
        $this->db->commit();

        return $insert;
    }

    /**
     * Make Read One Notification.
     *
     * @param  Notification $notification
     * @return bool|Notification
     */
    public function readOne(Notification $notification)
    {
        $notification->read = 1;

        if ($notification->save()) {
            return $notification;
        }

        return false;
    }

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
    public function readLimit($toId, $entity, $numbers, $order)
    {
        $notifications = $this->notification->withNotRead()
            ->wherePolymorphic($toId, $entity)
            ->limit($numbers)
            ->orderBy('id', $order)
            ->lists('id');

        return $this->notification->whereIn('id', $notifications)
            ->update(['read' => 1]);
    }

    /**
     * Make read all notification not read.
     *
     * @param $toId
     * @param $entity
     * @return int
     */
    public function readAll($toId, $entity)
    {
        return $this->notification->withNotRead()
            ->wherePolymorphic($toId, $entity)
            ->update(['read' => 1]);
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
        return $this->notification->where('id', $notificationId)->delete();
    }

    /**
     * Delete All notifications about the
     * current user.
     *
     * @param $toId int
     * @param $entity
     * @return bool
     */
    public function deleteAll($toId, $entity)
    {
        $query = $this->db->table(
            $this->notification->getTable()
        );

        return $this->notification->scopeWherePolymorphic($query, $toId, $entity)
            ->delete();
    }

    /**
     * Delete All notifications from a
     * defined category.
     *
     * @param $categoryName int
     * @param $expired       Bool
     * @return bool
     */
    public function deleteByCategory($categoryName, $expired = false)
    {
        $query = $this->notification->whereHas(
            'body',
            function ($query) use ($categoryName) {
                $query->where('name', $categoryName);
            }
        );

        if ($expired == true) {
            return $query->onlyExpired()->delete();
        }

        return $query->delete();
    }

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
    public function deleteLimit($userId, $entity, $number, $order)
    {
        $notificationsIds = $this->notification
            ->wherePolymorphic($userId, $entity)
            ->orderBy('id', $order)
            ->select('id')
            ->limit($number)
            ->lists('id');

        if (count($notificationsIds) == 0) {
            return false;
        }

        return $this->notification->whereIn('id', $notificationsIds)
            ->delete();
    }

    /**
     * Retrieve notifications not Read
     * You can also limit the number of
     * Notification if you don't it will get all.
     *
     * @param              $toId
     * @param              $entity
     * @param  int|null    $limit
     * @param  int|null    $paginate
     * @param  string      $orderDate
     * @param Closure|null $filterScope
     * @return mixed
     */
    public function getNotRead(
        $toId,
        $entity,
        $limit = null,
        $paginate = null,
        $orderDate = 'desc',
        Closure $filterScope = null
    ) {
        $query = $this->notification->with('body', 'from')
            ->wherePolymorphic($toId, $entity)
            ->withNotRead()
            ->orderBy('read', 'ASC')
            ->orderBy('created_at', $orderDate);

        if ($limit && ! $paginate) {
            $query->limit($limit);
        }

        $query = $this->applyFilter($filterScope, $query);

        if (is_int(intval($paginate)) && $paginate) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

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
    ) {
        $query = $this->notification->with('body', 'from')
            ->wherePolymorphic($toId, $entity)
            ->orderBy('read', 'ASC')
            ->orderBy('created_at', $orderDate);

        if ($limit && ! $paginate) {
            $query->limit($limit);
        }

        $query = $this->applyFilter($filterScope, $query);

        if (is_int(intval($paginate)) && $paginate) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * get number Notifications
     * not read.
     *
     * @param         $toId
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($toId, $entity, Closure $filterScope = null)
    {
        $query = $this->notification->wherePolymorphic($toId, $entity)
            ->withNotRead()
            ->select($this->db->raw('Count(*) as notRead'));

        $query = $this->applyFilter($filterScope, $query);

        return $query->count();
    }

    /**
     * Get last notification of the current
     * entity.
     *
     * @param         $toId
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($toId, $entity, Closure $filterScope = null)
    {
        $query = $this->notification->wherePolymorphic($toId, $entity)
            ->orderBy('created_at', 'DESC');

        $query = $this->applyFilter($filterScope, $query);

        return $query->first();
    }

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
    public function getLastNotificationByCategory($category, $toId, $entity, Closure $filterScope = null)
    {
        $query = $this->notification
            ->wherePolymorphic($toId, $entity)
            ->byCategory($category)
            ->orderBy('created_at', 'desc');

        $query = $this->applyFilter($filterScope, $query);

        return $query->first();
    }

    /**
     * Apply scope filters.
     *
     * @param Closure $filterScope
     * @param         $query
     */
    protected function applyFilter(Closure $filterScope = null, $query)
    {
        if (! $filterScope) {
            return $query;
        }

        $filterScope($query);

        return $query;
    }

    /**
     * Retrive all notifications, in a stack.
     * You can also limit the number of
     * Notifications if you don't, it will get all.
     *
     * @param           $stackId
     * @param  null     $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getStack(
        $stackId,
        $limit = null,
        $paginate = null,
        $orderDate = 'desc',
        Closure $filterScope = null
    ) {
        $query = $this->notification->with('body', 'from', 'to')
            ->byStack($stackId)
            ->orderBy('read', 'ASC')
            ->orderBy('created_at', $orderDate);

        if ($limit && ! $paginate) {
            $query->limit($limit);
        }

        $query = $this->applyFilter($filterScope, $query);

        if (is_int(intval($paginate)) && $paginate) {
            return $query->paginate($limit);
        }

        return $query->get();
    }
}
