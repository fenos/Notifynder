<?php

namespace Fenos\Notifynder;

use Closure;
use Fenos\Notifynder\Builder\NotifynderBuilder;

/**
 * Class Notifynder.
 *
 * Notifynder is a Facade Class that has
 * all the methods necessary to use the library.
 *
 * Notifynder allow you to have a flexible notification
 * management. It will provide you a nice and easy API
 * to store, retrieve and organise your notifications.
 */
interface Notifynder
{
    /**
     * Set the category of the
     * notification.
     *
     * @param $name
     * @return $this
     */
    public function category($name);

    /**
     * Define an entity when Notifynder is
     * used Polymorphically.
     *
     * @param $name
     * @return $this
     */
    public function entity($name);

    /**
     * Add a category.
     *
     * @param $name
     * @param $text
     * @return static
     */
    public function addCategory($name, $text);

    /**
     * Update a category.
     *
     * @param  array $updates
     * @param        $categoryId
     * @return mixed
     */
    public function updateCategory(array $updates, $categoryId);

    /**
     * Send notifications
     * Both multiple and single.
     *
     * @param  array $info
     * @return mixed
     */
    public function send($info = []);

    /**
     * Send immediately the notification
     * even if the queue is enabled.
     *
     * @param  array $info
     * @return mixed
     */
    public function sendNow($info = []);

    /**
     * Send One notification.
     *
     * @param  array $info
     * @return mixed
     */
    public function sendOne($info = []);

    /**
     * Send multiple notifications.
     *
     * @param  array                $info
     * @return Senders\SendMultiple
     */
    public function sendMultiple($info = []);

    /**
     * Send a group of notifications.
     *
     * @param $groupName
     * @param $info
     * @return mixed
     */
    public function sendGroup($groupName, $info = []);

    /**
     * Read one notification.
     *
     * @param $notificationId
     * @return bool|Models\Notification
     */
    public function readOne($notificationId);

    /**
     * Read notification in base the number
     * Given.
     *
     * @param         $toId
     * @param         $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimit($toId, $numbers, $order = 'ASC');

    /**
     * Read all notifications of the given
     * entity.
     *
     * @param $toId
     * @return int
     */
    public function readAll($toId);

    /**
     * Delete a single notification.
     *
     * @param $notificationId
     * @return bool
     */
    public function delete($notificationId);

    /**
     * Delete number of notifications
     * specified of the given entity.
     *
     * @param         $toId
     * @param         $number
     * @param  string $order
     * @return mixed
     */
    public function deleteLimit($toId, $number, $order = 'ASC');

    /**
     * Delete all notifications
     * of the the given entity.
     *
     * @param $toId
     * @return bool
     */
    public function deleteAll($toId);

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
     * Get Notifications not read
     * of the given entity.
     *
     * @param         $toId
     * @param  null   $limit
     * @param  bool   $paginate
     * @param  string $order
     * @param Closure $filterScope
     * @return mixed
     */
    public function getNotRead($toId, $limit = null, $paginate = false, $order = 'desc', Closure $filterScope = null);

    /**
     * Get all notifications of the
     * given entity.
     *
     * @param         $toId
     * @param  null   $limit
     * @param  bool   $paginate
     * @param  string $order
     * @param Closure $filterScope
     * @return mixed
     */
    public function getAll($toId, $limit = null, $paginate = false, $order = 'desc', Closure $filterScope = null);

    /**
     * Get number of notification not read
     * of the given entity.
     *
     * @param         $toId
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($toId, Closure $filterScope = null);

    /**
     * Find Notification by ID.
     *
     * @param $notificationId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function findNotificationById($notificationId);

    /**
     * Get last notification of the given
     * entity, second parameter can filter by
     * category.
     *
     * @param         $toId
     * @param null    $category
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($toId, $category = null, Closure $filterScope = null);

    /**
     * Add category to a group
     * giving the names of them.
     *
     * @param $groupName
     * @param $categoryName
     * @return mixed
     */
    public function addCategoryToGroupByName($groupName, $categoryName);

    /**
     * Add category to a group
     * giving the ids of them.
     *
     * @param $groupId
     * @param $categoryId
     * @return mixed
     */
    public function addCategoryToGroupById($groupId, $categoryId);

    /**
     * Add categories to a group having as first parameter
     * the name of the group, and others as name
     * categories.
     *
     * @return mixed
     */
    public function addCategoriesToGroup();

    /**
     * Fire method for fire listeners
     * of logic.
     *
     * @param  string     $key
     * @param  string     $categoryName
     * @param  mixed|null $values
     * @return mixed|null
     */
    public function fire($key, $categoryName, $values = []);

    /**
     * Associate events to categories.
     *
     * @param        $data
     * @param  array $delegation
     * @return mixed
     */
    public function delegate(array $delegation, $data = []);

    /**
     * Boot Listeners.
     *
     * @param array $listeners
     */
    public function bootListeners(array $listeners);

    /**
     * Get instance of the notifynder builder.
     *
     * @return NotifynderBuilder
     */
    public function builder();

    /**
     * Extend a custom sender method.
     *
     * @param           $name
     * @param  callable $registrar
     * @return $this
     */
    public function extend($name, $registrar);

    /**
     * Return the Id of the category.
     *
     * @return mixed
     */
    public function id();

    /**
     * Get the categoriesContainer property.
     *
     * @param $name
     * @return array
     */
    public function getCategoriesContainer($name);

    /**
     * Define which method
     * the event dispatcher has
     * to send the notifications
     * as default we have 'send' so will be
     * $notifynder->send() if u pass 'sendCustom'
     * it will be like $notifynder->sendCustom().
     *
     * @param $customSenderName
     * @return $this
     */
    public function dispatchWith($customSenderName);
}
