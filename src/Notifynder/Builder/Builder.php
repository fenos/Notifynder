<?php

namespace Fenos\Notifynder\Builder;

use Closure;
use ArrayAccess;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Fenos\Notifynder\Helpers\TypeChecker;
use Fenos\Notifynder\Models\NotificationCategory;
use Fenos\Notifynder\Exceptions\UnvalidNotificationException;

/**
 * Class Builder.
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
     * Set the category for this notification.
     *
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return $this
     */
    public function category($category)
    {
        $categoryId = NotificationCategory::getIdByCategory($category);
        $this->setNotificationData('category_id', $categoryId);

        return $this;
    }

    /**
     * Set the sender for this notification.
     *
     * @return $this
     */
    public function from()
    {
        $args = func_get_args();
        $this->setEntityData($args, 'from');

        return $this;
    }

    /**
     * Set the sender anonymous for this notification.
     *
     * @return $this
     */
    public function anonymous()
    {
        $this->setNotificationData('from_type', null);
        $this->setNotificationData('from_id', null);

        return $this;
    }

    /**
     * Set the receiver for this notification.
     *
     * @return $this
     */
    public function to()
    {
        $args = func_get_args();
        $this->setEntityData($args, 'to');

        return $this;
    }

    /**
     * Set the url for this notification.
     *
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
     * Set the expire date for this notification.
     *
     * @param Carbon|\DateTime $datetime
     * @return $this
     */
    public function expire(Carbon $datetime)
    {
        TypeChecker::isDate($datetime);
        $carbon = new Carbon($datetime);
        $this->setNotificationData('expires_at', $carbon);

        return $this;
    }

    /**
     * Set the extra values for this notification.
     * You can extend the existing extras or override them - important for multiple calls of extra() on one notification.
     *
     * @param array $extra
     * @param bool $override
     * @return $this
     */
    public function extra(array $extra = [], $override = true)
    {
        TypeChecker::isArray($extra);
        if (! $override) {
            $extra = array_merge($this->getNotificationData('extra', []), $extra);
        }
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
     * Set polymorphic model values.
     *
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
     * Get a single value of this notification.
     *
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    protected function getNotificationData($key, $default = null)
    {
        return $this->notification->get($key, $default);
    }

    /**
     * Set a single value of this notification.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setNotificationData($key, $value)
    {
        $this->notification->set($key, $value);
    }

    /**
     * Get the current notification.
     *
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
     * Add a notification to the notifications array.
     *
     * @param Notification $notification
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * Get all notifications.
     *
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
     * Loop over data and call the callback with a new Builder instance and the key and value of the iterated data.
     *
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
