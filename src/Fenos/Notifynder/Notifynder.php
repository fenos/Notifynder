<?php namespace Fenos\Notifynder;

use Fenos\Notifynder\Builder\NotifynderBuilder;
use Fenos\Notifynder\Categories\NotifynderCategory;
use Fenos\Notifynder\Groups\NotifynderGroup;
use Fenos\Notifynder\Handler\NotifynderHandler;
use Fenos\Notifynder\Notifications\NotifynderNotification;
use Fenos\Notifynder\Senders\NotifynderSender;

/**
 * Class Notifynder
 *
 * Notification system 2.0
 *
 * @package Fenos\Notifynder
 */
class Notifynder implements NotifynderInterface {

    /**
     * @var NotifynderCategory
     */
    protected $notifynderCategory;

    /**
     * @var array
     */
    protected $categoriesContainer = [];

    /**
     * @var \Fenos\Notifynder\Models\NotificationCategory
     */
    protected $category = null;

    /**
     * @var Senders\NotifynderSender
     */
    protected $notifynderSender;

    /**
     * @var Notifications\NotifynderNotification
     */
    protected $notification;

    /**
     * @var Handler\NotifynderHandler
     */
    protected $notifynderHandler;

    /**
     * @var Groups\NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var string | null
     */
    protected $entity;

    /**
     * @param NotifynderCategory                   $notifynderCategory
     * @param Senders\NotifynderSender             $notifynderSender
     * @param Notifications\NotifynderNotification $notification
     * @param Handler\NotifynderHandler            $notifynderHandler
     * @param Groups\NotifynderGroup               $notifynderGroup
     */
    function __construct(NotifynderCategory $notifynderCategory,
                         NotifynderSender $notifynderSender,
                         NotifynderNotification $notification,
                         NotifynderHandler $notifynderHandler,
                         NotifynderGroup $notifynderGroup)
    {
        $this->notifynderCategory = $notifynderCategory;
        $this->notifynderSender = $notifynderSender;
        $this->notification = $notification;
        $this->notifynderHandler = $notifynderHandler;
        $this->notifynderGroup = $notifynderGroup;
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
        // Check if the category is eager loaded
        if ($this->isEagerLoaded($name))
        {
            // Yes it is, split out the value from the array
            $this->category = $this->getCategoriesContainer()[$name];

            return $this;
        }

        // Otherwise ask to the db and give me the right category
        // associated with this name. If the category is not found
        // it throw CategoryNotFoundException
        $category = $this->notifynderCategory->findByName($name);

        $this->category = $category;

        // Set the category on the array
        $this->setCategoriesContainer($name,$category);

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
     * Send notifications
     * Both multiple and single
     *
     * @param array $info
     * @return mixed
     */
    public function send(array $info)
    {
        return $this->notifynderSender->send($info,$this->category);
    }

    /**
     * Send immediately the notification
     * even if the queue is enabled
     *
     * @param array $info
     * @return mixed
     */
    public function sendNow(array $info)
    {
        return $this->notifynderSender->sendNow($info,$this->category);
    }

    /**
     * Send One notification
     *
     * @param array $info
     * @return mixed
     */
    public function sendOne(array $info)
    {
        return $this->notifynderSender->sendOne($info,$this->category);
    }

    /**
     * Send multiple notifications
     *
     * @param array $info
     * @return Senders\SendMultiple
     */
    public function sendMultiple(array $info)
    {
        return $this->notifynderSender->sendMultiple($info,$this->category);
    }

    /**
     * Send a group of notifications
     *
     * @param $group_name
     * @param $info
     * @return mixed
     */
    public function sendGroup($group_name, $info)
    {
        return $this->notifynderSender->sendGroup($this,$group_name,$info);
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
     * @param        $to_id
     * @param        $numbers
     * @param string $order
     * @return mixed
     */
    public function readLimit($to_id,$numbers, $order = "ASC")
    {
        $notification = $this->notification->entity($this->entity);

        return $notification->readLimit($to_id,$numbers,$order);
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
     * @param        $to_id
     * @param        $number
     * @param string $order
     * @return mixed
     */
    public function deleteLimit($to_id,$number,$order = "ASC")
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->deleteLimit($to_id,$number,$order);
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
     * Get Notifications not read
     * of the given entity
     *
     * @param      $to_id
     * @param null $limit
     * @param bool $paginate
     * @return mixed
     */
    public function getNotRead($to_id, $limit = null, $paginate = false)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->getNotRead($to_id,$limit,$paginate);
    }

    /**
     * Get all notifications of the
     * given entity
     *
     * @param      $to_id
     * @param null $limit
     * @param bool $paginate
     * @return mixed
     */
    public function getAll($to_id, $limit = null, $paginate = false)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->getAll($to_id,$limit,$paginate);
    }

    /**
     * Get number of notification not read
     * of the given entity
     *
     * @param $to_id
     * @return mixed
     */
    public function countNotRead($to_id)
    {
        $notifications = $this->notification->entity($this->entity);

        return $notifications->countNotRead($to_id);
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
     * Add category to a group
     * giving the names of them
     *
     * @param $gorup_name
     * @param $category_name
     * @return mixed
     */
    public function addCategoryToGroupByName($gorup_name,$category_name)
    {
        return $this->notifynderGroup->addCategoryToGroupByName($gorup_name,$category_name);
    }

    /**
     * Add category to a group
     * giving the ids of them
     *
     * @param $gorup_id
     * @param $category_id
     * @return mixed
     */
    public function addCategoryToGroupById($gorup_id,$category_id)
    {
        return $this->notifynderGroup->addCategoryToGroupById($gorup_id,$category_id);
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
    public function fire($key,$category_name, $values = null)
    {
        return $this->notifynderHandler->fire($this,$key,$category_name,$values);
    }

    /**
     * Associate events to categories
     *
     * @param       $data
     * @param array $delegation
     * @return mixed
     */
    public function delegate($data = null,array $delegation)
    {
        return $this->notifynderHandler->delegate($this,$data,$delegation);
    }

    /**
     * Boot Listeners
     *
     * @return void
     */
    public function bootListeners()
    {
        $this->notifynderHandler->boot();
    }

    /**
     * Get instance of the notifynder builder
     *
     * @return NotifynderBuilder
     */
    public function builder()
    {
        return new NotifynderBuilder(
            $this->notifynderCategory
        );
    }

    /**
     * Check if the category is eager Loaded
     *
     * @param $name
     * @return bool
     */
    public function isEagerLoaded($name)
    {
        return array_key_exists($name, $this->getCategoriesContainer());
    }

    /**
     * Return the Id of the category
     *
     * @return mixed
     */
    public function id()
    {
        return $this->category->id();
    }

    /**
     * Push a category in the categoriesContainer
     * property
     *
     * @param       $name
     * @param array $categoriesContainer
     */
    public function setCategoriesContainer($name,$categoriesContainer)
    {
        $this->categoriesContainer[$name] = $categoriesContainer;
    }

    /**
     * Get the categoriesContainer property
     *
     * @return array
     */
    public function getCategoriesContainer()
    {
        return $this->categoriesContainer;
    }
} 