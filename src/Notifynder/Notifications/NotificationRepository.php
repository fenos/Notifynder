<?php namespace Fenos\Notifynder\Notifications;

use Closure;
use Fenos\Notifynder\Contracts\NotificationDB;
use Fenos\Notifynder\Models\Notification;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderDB;

/**
 * Class NotificationRepository
 *
 * @package Fenos\Notifynder\Senders
 */
class NotificationRepository implements NotificationDB
{

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
    public function __construct(
        Notification $notification,
        DatabaseManager $db
    ) {
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
     * @param  array $info
     * @return Notification
     */
    public function storeSingle(array $info)
    {
        return $this->notification->create($info);
    }

    /**
     * Save multiple notifications sent
     * at once
     *
     * @param  array $info
     * @return mixed
     */
    public function storeMultiple(array $info)
    {
        return $this->db->table(
            $this->notification->getTable()
        )->insert($info);
    }

    /**
     * Make Read One Notification
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
     * Given
     *
     * @param $to_id
     * @param $entity
     * @param $numbers
     * @param $order
     * @return int
     */
    public function readLimit($to_id, $entity, $numbers, $order)
    {
        $notifications = $this->notification->withNotRead()
            ->wherePolymorphic($to_id, $entity)
            ->limit($numbers)
            ->orderBy('id', $order)
            ->lists('id');

        return $this->notification->whereIn('id', $notifications)
            ->update(['read' => 1]);
    }

    /**
     * Make read all notification not read
     *
     * @param $to_id
     * @param $entity
     * @return int
     */
    public function readAll($to_id, $entity)
    {
        return $this->notification->withNotRead()
            ->wherePolymorphic($to_id, $entity)
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
        return $this->notification->where('id', $notification_id)->delete();
    }

    /**
     * Delete All notifications about the
     * current user
     *
     * @param $to_id int
     * @param $entity
     * @return Bool
     */
    public function deleteAll($to_id, $entity)
    {
        $query = $this->db->table(
            $this->notification->getTable()
        );

        return $this->notification->scopeWherePolymorphic($query, $to_id, $entity)
            ->delete();
    }

    /**
     * Delete All notifications from a
     * defined category
     *
     * @param $category_name int
     * @param $expired       Bool
     * @return Bool
     */
    public function deleteByCategory($category_name, $expired = false)
    {
        $query = $this->notification->whereHas(
            'body',
            function ($q) use ($category_name) {
                $q->where('name', $category_name);
            }
        );

        if ($expired == true) {
            return $query->onlyExpired()->delete();
        }

        return $query->delete();
    }

    /**
     *
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
    public function deleteLimit($user_id, $entity, $number, $order)
    {
        $notifications_ids = $this->notification
            ->wherePolymorphic($user_id, $entity)
            ->orderBy('id', $order)
            ->select('id')
            ->limit($number)
            ->lists('id');

        if (count($notifications_ids) == 0) {
            return false;
        }

        return $this->notification->whereIn('id', $notifications_ids)
            ->delete();
    }

    /**
     * Retrive notifications not Read
     * You can also limit the number of
     * Notification if you don't it will get all
     *
     * @param              $to_id
     * @param              $entity
     * @param  int|null    $limit
     * @param  int|null    $paginate
     * @param  string      $orderDate
     * @param Closure|null $filterScope
     * @return mixed
     */
    public function getNotRead(
        $to_id,
        $entity,
        $limit = null,
        $paginate = null,
        $orderDate = 'desc',
        Closure $filterScope = null
    ) {
        $query = $this->notification->with('body', 'from')
            ->wherePolymorphic($to_id, $entity)
            ->withNotRead()
            ->orderBy('read', 'ASC')
            ->orderBy('created_at', $orderDate);

        if ($limit && !$paginate) {
            $query->limit($limit);
        }

        $query = $this->applyFilter($filterScope, $query);

        if (is_int(intval($paginate)) && $paginate) {
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * Retrive all notifications, not read
     * in first.
     * You can also limit the number of
     * Notifications if you don't, it will get all
     *
     * @param           $to_id
     * @param           $entity
     * @param  null     $limit
     * @param  int|null $paginate
     * @param  string   $orderDate
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getAll(
        $to_id,
        $entity,
        $limit = null,
        $paginate = null,
        $orderDate = 'desc',
        Closure $filterScope = null
    ) {
        $query = $this->notification->with('body', 'from')
            ->wherePolymorphic($to_id, $entity)
            ->orderBy('read', 'ASC')
            ->orderBy('created_at', $orderDate);

        if ($limit && !$paginate) {
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
     * not read
     *
     * @param         $to_id
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($to_id, $entity, Closure $filterScope = null)
    {
        $query = $this->notification->wherePolymorphic($to_id, $entity)
            ->withNotRead()
            ->select($this->db->raw('Count(*) as notRead'));

        $query = $this->applyFilter($filterScope, $query);

        return $query->count();
    }

    /**
     * Get last notification of the current
     * entity
     *
     * @param         $to_id
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($to_id, $entity, Closure $filterScope = null)
    {
        $query = $this->notification->wherePolymorphic($to_id, $entity)
            ->orderBy('created_at', 'DESC');

        $query = $this->applyFilter($filterScope, $query);

        return $query->first();
    }

    /**
     * Get last notification of the current
     * entity of a specific category
     *
     * @param         $category
     * @param         $to_id
     * @param         $entity
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotificationByCategory($category, $to_id, $entity, Closure $filterScope = null)
    {
        $query = $this->notification
            ->wherePolymorphic($to_id, $entity)
            ->byCategory($category)
            ->orderBy('created_at', 'desc');

        $query = $this->applyFilter($filterScope, $query);

        return $query->first();
    }

    /**
     * Apply scope filters
     *
     * @param Closure $filterScope
     * @param         $query
     */
    protected function applyFilter(Closure $filterScope = null, $query)
    {
        if ( ! $filterScope) {
            return $query;
        }

        $filterScope($query);

        return $query;
    }
}
