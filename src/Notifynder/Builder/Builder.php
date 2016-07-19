<?php

namespace Fenos\Notifynder\Builder;

use ArrayAccess;
use Carbon\Carbon;
use Closure;
use Fenos\Notifynder\Exceptions\UnvalidNotificationException;
use Fenos\Notifynder\Helpers\TypeChecker;
use Fenos\Notifynder\Models\NotificationCategory;
use Illuminate\Database\Eloquent\Model;

class Builder implements ArrayAccess
{
    protected $notification;

    protected $notifications = [];

    protected $typeChecker;

    public function __construct()
    {
        $this->notification = new Notification();
        $this->typeChecker = new TypeChecker();
    }

    public function category($category)
    {
        $categoryId = $category;
        if ($category instanceof NotificationCategory) {
            $categoryId = $category->getKey();
        } elseif (!is_numeric($category)) {
            $categoryId = NotificationCategory::byName($category)->firstOrFail()->getKey();
        }

        $this->setNotificationData('category_id', $categoryId);

        return $this;
    }

    public function from()
    {
        $args = func_get_args();
        $this->setEntityData($args, 'from');

        return $this;
    }

    public function anonymous()
    {
        $this->setNotificationData('from_type', null);
        $this->setNotificationData('from_id', null);

        return $this;
    }

    public function to()
    {
        $args = func_get_args();
        $this->setEntityData($args, 'to');

        return $this;
    }

    public function url($url)
    {
        $this->typeChecker->isString($url);
        $this->setNotificationData('url', $url);

        return $this;
    }

    public function expire($datetime)
    {
        $this->typeChecker->isDate($datetime);
        $this->setNotificationData('expires_at', $datetime);

        return $this;
    }

    public function extra(array $extra = [])
    {
        $this->typeChecker->isArray($extra);
        $this->setNotificationData('extra', $extra);

        return $this;
    }

    public function setDates()
    {
        $date = Carbon::now();

        $this->setNotificationData('updated_at', $date);
        $this->setNotificationData('created_at', $date);
    }

    public function setField($key, $value)
    {
        $additionalFields = notifynder_config()->getAdditionalFields();
        if (in_array($key, $additionalFields)) {
            $this->setNotificationData($key, $value);
        }

        return $this;
    }

    protected function setEntityData($entity, $property)
    {
        if (is_array($entity) && count($entity) == 2) {
            $this->typeChecker->isString($entity[0]);
            $this->typeChecker->isNumeric($entity[1]);

            $type = $entity[0];
            $id = $entity[1];
        } elseif ($entity[0] instanceof Model) {
            $type = $entity[0]->getMorphClass();
            $id = $entity[0]->getKey();
        } else {
            $this->typeChecker->isNumeric($entity[0]);

            $type = notifynder_config()->getNotifiedModel();
            $id = $entity[0];
        }

        $this->setNotificationData("{$property}_type", $type);
        $this->setNotificationData("{$property}_id", $id);
    }

    protected function setNotificationData($key, $value)
    {
        $this->notification->set($key, $value);
    }

    public function getNotification()
    {
        if (!$this->notification->isValid()) {
            throw new UnvalidNotificationException($this->notification);
        }

        $this->setDates();

        return $this->notification;
    }

    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    public function getNotifications()
    {
        if (count($this->notifications) == 0) {
            $this->addNotification($this->getNotification());
        }

        return $this->notifications;
    }

    public function loop($data, Closure $callback)
    {
        $this->typeChecker->isIterable($data);

        foreach ($data as $key => $value) {
            $builder = new static();
            $callback($builder, $value, $key);
            $this->addNotification($builder->getNotification());
        }

        return $this;
    }

    public function offsetExists($offset)
    {
        return $this->notification->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->notification->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->notification->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->notification->offsetUnset($offset);
    }
}
