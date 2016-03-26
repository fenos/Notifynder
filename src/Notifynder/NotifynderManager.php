<?php namespace Fenos\Notifynder;

use BadMethodCallException;
use Closure;
use Fenos\Notifynder\Builder\NotifynderBuilder;
use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Contracts\NotifynderDispatcher;
use Fenos\Notifynder\Contracts\NotifynderGroup;
use Fenos\Notifynder\Contracts\NotifynderNotification;
use Fenos\Notifynder\Contracts\NotifynderSender;
use InvalidArgumentException;

/**
 * Class Notifynder
 *
 * Notifynder is a Facade Class that has
 * all the methods necesessary to use the library.
 *
 * Notifynder allow you to have a flexible notification
 * management. It will provide you a nice and easy API
 * to store, retrieve and organise your notifications.
 *
 * @package Fenos\Notifynder
 */
class NotifynderManager extends NotifynderBuilder implements Notifynder
{

    /**
     * Version Notifynder
     *
     * @var string
     */
    const VERSION = '3.1.0';

    /**
     * @var NotifynderCategory
     */
    protected $notifynderCategory;

    /**
     * @var array
     */
    protected $categoriesContainer = [];

    /**
     * @var Models\NotificationCategory|null
     */
    protected $defaultCategory;

    /**
     * @var NotifynderSender
     */
    protected $notifynderSender;

    /**
     * @var NotifynderNotification
     */
    protected $notification;

    /**
     * @var NotifynderDispatcher
     */
    protected $notifynderDispatcher;

    /**
     * This sender method
     * will be used on the dispatcher
     *
     * @var string
     */
    protected $eventSender = 'send';

    /**
     * @var NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var string | null
     */
    protected $entity;

    /**
     * @param NotifynderCategory     $notifynderCategory
     * @param NotifynderSender       $notifynderSender
     * @param NotifynderNotification $notification
     * @param NotifynderDispatcher   $notifynderDispatcher
     * @param NotifynderGroup        $notifynderGroup
     */
    public function __construct(NotifynderCategory $notifynderCategory,
                         NotifynderSender $notifynderSender,
                         NotifynderNotification $notification,
                         NotifynderDispatcher $notifynderDispatcher,
                         NotifynderGroup $notifynderGroup)
    {
        $this->notifynderCategory = $notifynderCategory;
        $this->notifynderSender = $notifynderSender;
        $this->notification = $notification;
        $this->notifynderDispatcher = $notifynderDispatcher;
        $this->notifynderGroup = $notifynderGroup;

        parent::__construct($notifynderCategory);
    }

    /**
     * Set the category of the
     * notification
     *
     * @param $name
     * @return $this
     */
    public function category($name)
    {
        // Check if the category is lazy loaded
        if ($this->isLazyLoaded($name)) {
            // Yes it is, split out the value from the array
            $this->defaultCategory = $this->getCategoriesContainer($name);

            // set category on the builder
            parent::category($this->defaultCategory->id);

            return $this;
        }

        // Otherwise ask to the db and give me the right category
        // associated with this name. If the category is not found
        // it throw CategoryNotFoundException
        $category = $this->notifynderCategory->findByName($name);

        $this->defaultCategory = $category;

        // Set the category on the array
        $this->setCategoriesContainer($name, $category);

        // set category on the builder
        parent::category($category->id);

        return $this;
    }

    /**
     * Define an entity when Notifynder is
     * used Polymorpically
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
     * Add a category
     *
     * @param $name
     * @param $text
     * @return static
     */
    public function addCategory($name, $text)
    {
        return $this->notifynderCategory->add($name, $text);
    }

    /**
     * Update a category
     *
     * @param  array $updates
     * @param        $id
     * @return mixed
     */
    public function updateCategory(array $updates, $id)
    {
        return $this->notifynderCategory->update($updates, $id);
    }

    /**
     * Send notifications
     * Both multiple and single
     *
     * @param  array $info
     * @return mixed
     */
    public function send($info = [])
    {
        $info = (count($info) > 0) ? $info : $this->toArray();

        $notificationSent = $this->notifynderSender->send($info, $this->defaultCategory);

        $this->refresh();

        return $notificationSent;
    }

    /**
     * Send immediately the notification
     * even if the queue is enabled
     *
     * @param  array $info
     * @return mixed
     */
    public function sendNow($info = [])
    {
        $info = (count($info) > 0) ? $info : $this->toArray();

        $notificationsSent = $this->notifynderSender->sendNow($info, $this->defaultCategory);

        $this->refresh();

        return $notificationsSent;
    }

    /**
     * Send One notification
     *
     * @param  array $info
     * @return mixed
     */
    public function sendOne($info = [])
    {
        $info = (count($info) > 0) ? $info : $this->toArray();

        $notificationSent = $this->notifynderSender->sendOne($info, $this->defaultCategory);

        $this->refresh();

        return $notificationSent;
    }

    /**
     * Send multiple notifications
     *
     * @param  array                $info
     * @return Senders\SendMultiple
     */
    public function sendMultiple($info = [])
    {
        $info = (count($info) > 0) ? $info : $this->toArray();

        $notificationsSent = $this->notifynderSender->sendMultiple($info, $this->defaultCategory);

        $this->refresh();

        return $notificationsSent;
    }

    /**
     * Send a group of notifications
     *
     * @param $group_name
     * @param $info
     * @return mixed
     */
    public function sendGroup($group_name, $info = [])
    {
        $info = (count($info) > 0) ? $info : $this->toArray();

        $notificationsSent = $this->notifynderSender->sendGroup($this, $group_name, $info);

        $this->refresh();

        return $notificationsSent;
    }

    /**
     * Read one notification
     *
     * @param $notification_id
     * @return bool|Models\Notification
     */
    public function readOne($notification_id)
    {
        return $this->notification->readOne($notification_id);
    }

    /**
     * Read notification in base the number
     * Given
     *
     * @param         $to_id
     * @param         $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimit($to_id, $numbers, $order = "ASC")
    {
        $notification = $this->notification->entity($this->entity);

        return $notification->readLimit($to_id, $numbers, $order);
    }

    /**
     * Read all notifications of the given
     * entity
     *
     * @param $to_id
     * @return Number
     */
    public function readAll($to_id)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->readAll($to_id);
    }

    /**
     * Delete a single notification
     *
     * @param $notification_id
     * @return Bool
     */
    public function delete($notification_id)
    {
        return $this->notification->delete($notification_id);
    }

    /**
     * Delete number of notifications
     * secified of the given entity
     *
     * @param         $to_id
     * @param         $number
     * @param  string $order
     * @return mixed
     */
    public function deleteLimit($to_id, $number, $order = "ASC")
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->deleteLimit($to_id, $number, $order);
    }

    /**
     * Delete all notifications
     * of the the given entity
     *
     * @param $to_id
     * @return Bool
     */
    public function deleteAll($to_id)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->deleteAll($to_id);
    }

    /**
     * Delete All notifications from a
     * defined category
     *
     * @param $category_name string
     * @param $expired Bool
     * @return Bool
     */
    public function deleteByCategory($category_name, $expired = false)
    {
        return $this->notification->deleteByCategory($category_name, $expired);
    }

    /**
     * Get Notifications not read
     * of the given entity
     *
     * @param           $to_id
     * @param  null     $limit
     * @param  null|int $paginate
     * @param  string   $order
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getNotRead($to_id, $limit = null, $paginate = null, $order = "desc", Closure $filterScope = null)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->getNotRead($to_id, $limit, $paginate, $order, $filterScope);
    }

    /**
     * Get all notifications of the
     * given entity
     *
     * @param           $to_id
     * @param  null     $limit
     * @param  int|null $paginate
     * @param  string   $order
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getAll($to_id, $limit = null, $paginate = null, $order = "desc", Closure $filterScope = null)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->getAll($to_id, $limit, $paginate,$order, $filterScope);
    }

    /**
     * Get number of notification not read
     * of the given entity
     *
     * @param         $to_id
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotRead($to_id,Closure $filterScope = null)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->countNotRead($to_id,$filterScope);
    }

    /**
     * Find Notification by ID
     *
     * @param $notification_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function findNotificationById($notification_id)
    {
        return $this->notification->find($notification_id);
    }

    /**
     * Get last notification of the given
     * entity, second parameter can filter by
     * category
     *
     * @param         $to_id
     * @param null    $category
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($to_id,$category = null,Closure $filterScope = null)
    {
        $notification = $this->notification->entity($this->entity);

        if ( is_null($category)) {
            return $notification->getLastNotification($to_id,$filterScope);
        }

        return $notification->getLastNotificationByCategory($category,$to_id,$filterScope);
    }

    /**
     * Add category to a group
     * giving the names of them
     *
     * @param $gorup_name
     * @param $category_name
     * @return mixed
     */
    public function addCategoryToGroupByName($gorup_name, $category_name)
    {
        return $this->notifynderGroup->addCategoryToGroupByName($gorup_name, $category_name);
    }

    /**
     * Add category to a group
     * giving the ids of them
     *
     * @param $gorup_id
     * @param $category_id
     * @return mixed
     */
    public function addCategoryToGroupById($gorup_id, $category_id)
    {
        return $this->notifynderGroup->addCategoryToGroupById($gorup_id, $category_id);
    }

    /**
     * Add categories to a group having as first parameter
     * the name of the group, and others as name
     * categories
     *
     * @return mixed
     */
    public function addCategoriesToGroup()
    {
        return $this->notifynderGroup->addMultipleCategoriesToGroup(func_get_args());
    }

    /**
     * Fire method for fire listeners
     * of logic
     *
     * @param  string     $key
     * @param  string     $category_name
     * @param  mixed|null $values
     * @return mixed|null
     */
    public function fire($key, $category_name, $values = [])
    {
        return $this->notifynderDispatcher->sendWith($this->eventSender)
                    ->fire($this, $key, $category_name, $values);
    }

    /**
     * Associate events to categories
     *
     * @param        $data
     * @param  array $delegation
     * @return mixed
     */
    public function delegate(array $delegation, $data = [])
    {
        return $this->notifynderDispatcher->delegate($this, $data, $delegation);
    }

    /**
     * Boot Listeners
     *
     * @param array $listeners
     */
    public function bootListeners(array $listeners)
    {
        $this->notifynderDispatcher->boot($listeners);
    }

    /**
     * Get instance of the notifynder builder
     *
     * @return NotifynderBuilder
     */
    public function builder()
    {
        return new parent($this->notifynderCategory);
    }

    /**
     * Extend a custom sender method
     *
     * @param           $name
     * @param  callable $registrar
     * @return $this
     */
    public function extend($name, $registrar)
    {
        if (! starts_with($name, 'send')) {
            $error = "The sender method must start with [send]";
            throw new InvalidArgumentException($error);
        }

        $this->notifynderSender->extend($name, $registrar);

        return $this;
    }

    /**
     * Check if the category is eager Loaded
     *
     * @param $name
     * @return bool
     */
    protected function isLazyLoaded($name)
    {
        return array_key_exists($name, $this->categoriesContainer);
    }

    /**
     * Return the Id of the category
     *
     * @return mixed
     */
    public function id()
    {
        return $this->defaultCategory->id;
    }

    /**
     * Push a category in the categoriesContainer
     * property
     *
     * @param       $name
     * @param array $categoriesContainer
     */
    protected function setCategoriesContainer($name, $categoriesContainer)
    {
        $this->categoriesContainer[$name] = $categoriesContainer;
    }

    /**
     * Get the categoriesContainer property
     *
     * @param $name
     * @return array
     */
    public function getCategoriesContainer($name)
    {
        return $this->categoriesContainer[$name];
    }

    /**
     * Define which method
     * the event dispatcher has
     * to send the notifications
     *
     * @param $customSenderName
     * @return $this
     */
    public function dipatchWith($customSenderName)
    {
        $this->eventSender = $customSenderName;

        return $this;
    }

    /**
     * Call the custom sender method
     *
     * @param $name
     * @param $arguments
     * @return void|mixed
     */
    public function __call($name, $arguments)
    {
        if (starts_with($name, 'send')) {

            $arguments = (isset($arguments[0])) ? $arguments[0] : $this->toArray();

            $notificationsSent = $this->notifynderSender->customSender($name,$arguments);
            $this->refresh();
            return $notificationsSent;
        }

        $error = "method [$name] not found in the class ".self::class;
        throw new BadMethodCallException($error);
    }

    /**
     * Set builder properties
     * When setting dynamic properties
     *
     * @param $name
     * @param $value
     */
    function __set($name, $value)
    {
        $this->offsetSet($name,$value);
    }

    /**
     * Get property from the
     * builder
     *
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->offsetGet($name);
    }
}
