<?php

namespace Fenos\Notifynder;

use Fenos\Notifynder\Builder\NotifynderBuilder;

/**
 * Class Notifynder.
 *
 * Notification system 2.0
 */
interface NotifynderInterface
{
    /**
     * Set the category of the
     * notification.
     *
     * @param $name
     *
     * @return $this
     */
    public function category($name);

    /**
     * Define an entity when Notifynder is
     * used Polymorpically.
     *
     * @param $name
     *
     * @return $this
     */
    public function entity($name);

    /**
     * Add a category.
     *
     * @param $name
     * @param $text
     *
     * @return static
     */
    public function addCategory($name, $text);

    /**
     * Update a category.
     *
     * @param array $updates
     * @param       $id
     *
     * @return mixed
     */
    public function updateCategory(array $updates, $id);

    /**
     * Send notifications
     * Both multiple and single.
     *
     * @param array $info
     *
     * @return mixed
     */
    public function send(array $info);

    /**
     * Send immediately the notification
     * even if the queue is enabled.
     *
     * @param array $info
     *
     * @return mixed
     */
    public function sendNow(array $info);

    /**
     * Send One notification.
     *
     * @param array $info
     *
     * @return mixed
     */
    public function sendOne(array $info);

    /**
     * Send multiple notifications.
     *
     * @param array $info
     *
     * @return Senders\SendMultiple
     */
    public function sendMultiple(array $info);

    /**
     * Send a group of notifications.
     *
     * @param $group_name
     * @param $info
     *
     * @return mixed
     */
    public function sendGroup($group_name, $info);

    /**
     * Read one notification.
     *
     * @param $notification_id
     *
     * @return bool|Models\Notification
     */
    public function readOne($notification_id);

    /**
     * Read notification in base the number
     * Given.
     *
     * @param        $to_id
     * @param        $numbers
     * @param string $order
     *
     * @return mixed
     */
    public function readLimit($to_id, $numbers, $order = 'ASC');

    /**
     * Read all notifications of the given
     * entity.
     *
     * @param $to_id
     *
     * @return Number
     */
    public function readAll($to_id);

    /**
     * Delete a single notification.
     *
     * @param $notification_id
     *
     * @return bool
     */
    public function delete($notification_id);

    /**
     * Delete number of notifications
     * secified of the given entity.
     *
     * @param        $to_id
     * @param        $number
     * @param string $order
     *
     * @return mixed
     */
    public function deleteLimit($to_id, $number, $order = 'ASC');

    /**
     * Delete all notifications
     * of the the given entity.
     *
     * @param $to_id
     *
     * @return bool
     */
    public function deleteAll($to_id);

    /**
     * Get Notifications not read
     * of the given entity.
     *
     * @param      $to_id
     * @param null $limit
     * @param bool $paginate
     *
     * @return mixed
     */
    public function getNotRead($to_id, $limit = null, $paginate = false);

    /**
     * Get all notifications of the
     * given entity.
     *
     * @param      $to_id
     * @param null $limit
     * @param bool $paginate
     *
     * @return mixed
     */
    public function getAll($to_id, $limit = null, $paginate = false);

    /**
     * Get number of notification not read
     * of the given entity.
     *
     * @param $to_id
     *
     * @return mixed
     */
    public function countNotRead($to_id);

    /**
     * Find Notification by ID.
     *
     * @param $notification_id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function findNotificationById($notification_id);

    /**
     * Add category to a group
     * giving the names of them.
     *
     * @param $gorup_name
     * @param $category_name
     *
     * @return mixed
     */
    public function addCategoryToGroupByName($gorup_name, $category_name);

    /**
     * Add category to a group
     * giving the ids of them.
     *
     * @param $gorup_id
     * @param $category_id
     *
     * @return mixed
     */
    public function addCategoryToGroupById($gorup_id, $category_id);

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
     * @param string     $key
     * @param string     $category_name
     * @param mixed|null $values
     *
     * @return mixed|null
     */
    public function fire($key, $category_name, $values = null);

    /**
     * Associate events to categories.
     *
     * @param       $data
     * @param array $delegation
     *
     * @return mixed
     */
    public function delegate($data, array $delegation);

    /**
     * Boot Listeners.
     *
     * @return void
     */
    public function bootListeners();

    /**
     * Get instance of the notifynder builder.
     *
     * @return NotifynderBuilder
     */
    public function builder();

    /**
     * Return the Id of the category.
     *
     * @return mixed
     */
    public function id();

    /**
     * Push a category in the categoriesContainer
     * property.
     *
     * @param       $name
     * @param array $categoriesContainer
     */
    public function setCategoriesContainer($name, $categoriesContainer);
}
