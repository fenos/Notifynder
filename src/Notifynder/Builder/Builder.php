<?php

namespace Fenos\Notifynder\Builder;

use ArrayAccess;
use Carbon\Carbon;
use Closure;
use Fenos\Notifynder\Exceptions\UnvalidNotificationException;
use Fenos\Notifynder\Helpers\TypeChecker;
use Fenos\Notifynder\Models\NotificationCategory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Builder
 * @package Fenos\Notifynder\Builder
 */
class Builder implements ArrayAccess
{
    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @var array
     */
    protected $notifications = [];

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->notification = new Notification();
    }

    /**
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return $this
     */
    public function category($category)
    {
        $categoryId = $category;
        if ($category instanceof NotificationCategory) {
            $categoryId = $category->getKey();
        } elseif (! is_numeric($category)) {
            $categoryId = NotificationCategory::byName($category)->firstOrFail()->getKey();
        }

        $this->setNotificationData('category_id', $categoryId);

        return $this;
    }

    /**
     * @return $this
     */
    public function from()
    {
        $args = func_get_args();
        $this->setEntityData($args, 'from');

        return $this;
    }

    /**
     * @return $this
     */
    public function anonymous()
    {
        $this->setNotificationData('from_type', null);
        $this->setNotificationData('from_id', null);

        return $this;
    }

    /**
     * @return $this
     */
    public function to()
    {
        $args = func_get_args();
        $this->setEntityData($args, 'to');

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function url($url)
    {
        TypeChecker::isString($url);
        $this->setNotificationData('url', $url);

        return $this;
    }

    /**
     * @param Carbon|\DateTime $datetime
     * @return $this
     */
    public function expire($datetime)
    {
        TypeChecker::isDate($datetime);
        $carbon = new Carbon($datetime);
        $this->setNotificationData('expires_at', $carbon);

        return $this;
    }

    /**
     * @param array $extra
     * @return $this
     */
    public function extra(array $extra = [])
    {
        TypeChecker::isArray($extra);
        $this->setNotificationData('extra', $extra);

        return $this;
    }

    /**
     * Set updated_at and created_at fields.
     */
    public function setDates()
    {
        $date = Carbon::now();

        $this->setNotificationData('updated_at', $date);
        $this->setNotificationData('created_at', $date);
    }

    /**
     * Set a single field value.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setField($key, $value)
    {
        $additionalFields = notifynder_config()->getAdditionalFields();
        if (in_array($key, $additionalFields)) {
            $this->setNotificationData($key, $value);
        }

        return $this;
    }

    /**
     * @param array $entity
     * @param string $property
     */
    protected function setEntityData($entity, $property)
    {
        if (is_array($entity) && count($entity) == 2) {
            TypeChecker::isString($entity[0]);
            TypeChecker::isNumeric($entity[1]);

            $type = $entity[0];
            $id = $entity[1];
        } elseif ($entity[0] instanceof Model) {
            $type = $entity[0]->getMorphClass();
            $id = $entity[0]->getKey();
        } else {
            TypeChecker::isNumeric($entity[0]);

            $type = notifynder_config()->getNotifiedModel();
            $id = $entity[0];
        }

        $this->setNotificationData("{$property}_type", $type);
        $this->setNotificationData("{$property}_id", $id);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    protected function setNotificationData($key, $value)
    {
        $this->notification->set($key, $value);
    }

    /**
     * @return Notification
     * @throws UnvalidNotificationException
     */
    public function getNotification()
    {
        if (! $this->notification->isValid()) {
            throw new UnvalidNotificationException($this->notification);
        }

        $this->setDates();

        return $this->notification;
    }

    /**
     * @param Notification $notification
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * @return array
     * @throws UnvalidNotificationException
     */
    public function getNotifications()
    {
        if (count($this->notifications) == 0) {
            $this->addNotification($this->getNotification());
        }

        return $this->notifications;
    }

    /**
     * @param array|\Traversable $data
     * @param Closure $callback
     * @return $this
     * @throws UnvalidNotificationException
     */
    public function loop($data, Closure $callback)
    {
        TypeChecker::isIterable($data);

        foreach ($data as $key => $value) {
            $builder = new static();
            $callback($builder, $value, $key);
            $this->addNotification($builder->getNotification());
        }

        return $this;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->notification->offsetExists($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->notification->offsetGet($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->notification->offsetSet($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->notification->offsetUnset($offset);
    }
}
