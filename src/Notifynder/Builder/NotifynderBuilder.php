<?php

namespace Fenos\Notifynder\Builder;

use ArrayAccess;
use Carbon\Carbon;
use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Exceptions\EntityNotIterableException;
use Fenos\Notifynder\Exceptions\IterableIsEmptyException;
use Fenos\Notifynder\Exceptions\NotificationBuilderException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Traversable;
use Closure;

/**
 * Class NotifynderBuilder.
 *
 * The builder is a main factor of Notifynder, it make sure
 * that the notification is decorated and validated before
 * are passed to the Sender Classes. It also helps you to
 * create multi notifications with the same simple and easy syntax.
 */
class NotifynderBuilder implements ArrayAccess
{
    use BuilderRules;

    /**
     * @var string notification to store
     */
    public $date;

    /**
     * Builder data.
     *
     * @var array
     */
    protected $notifications = [];

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var NotifynderCategory
     */
    private $notifynderCategory;

    /**
     * @param NotifynderCategory $notifynderCategory
     */
    public function __construct(NotifynderCategory $notifynderCategory)
    {
        $this->notifynderCategory = $notifynderCategory;
    }

    /**
     * Set who will send the notification.
     *
     * @return $this
     */
    public function from()
    {
        $from = func_get_args();

        $this->setEntityAction($from, 'from');

        return $this;
    }

    /**
     * Set who will receive the notification.
     *
     * @return $this
     */
    public function to()
    {
        $from = func_get_args();

        $this->setEntityAction($from, 'to');

        return $this;
    }

    /**
     * Set the url of the notification.
     *
     * @param $url
     * @return $this
     */
    public function url($url)
    {
        $this->isString($url);

        $this->setBuilderData('url', $url);

        return $this;
    }

    /**
     * Set expire time.
     *
     * @param $datetime
     * @return $this
     */
    public function expire($datetime)
    {
        $this->isCarbon($datetime);
        $this->setBuilderData('expire_time', $datetime);

        return $this;
    }

    /**
     * Set Category and covert it, to the id
     * if name of it given.
     *
     * @param $category
     * @return $this
     */
    public function category($category)
    {
        if (! is_numeric($category)) {
            $category = $this->notifynderCategory
                            ->findByName($category)->id;
        }

        $this->setBuilderData('category_id', $category);

        return $this;
    }

    /**
     * Set extra value.
     *
     * @param $extra
     * @return $this
     */
    public function extra(array $extra = [])
    {
        $this->isReadyArrToFormatInJson($extra);

        $jsonExtraValues = json_encode($extra);

        $this->setBuilderData('extra', $jsonExtraValues);

        return $this;
    }

    /**
     * Set additional fields value.
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setField($key, $value)
    {
        $this->setBuilderData($key, $value);

        return $this;
    }

    /**
     * Build the array with the builder inside
     * a Closure, it has more flexibility for
     * the generation of your array.
     *
     *
     * @param callable|Closure $closure
     * @return array|false
     * @throws NotificationBuilderException
     */
    public function raw(Closure $closure)
    {
        $builder = $closure($this);

        if (! is_null($builder)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * Loop the data for create
     * multi notifications array.
     *
     * @param          $dataToIterate
     * @param  Closure $builder
     * @return $this
     * @throws \Fenos\Notifynder\Exceptions\IterableIsEmptyException
     * @throws \Fenos\Notifynder\Exceptions\EntityNotIterableException
     */
    public function loop($dataToIterate, Closure $builder)
    {
        if (! $this->isIterable($dataToIterate)) {
            throw new EntityNotIterableException('The data passed must be iterable');
        }
        if (count($dataToIterate) <= 0) {
            throw new IterableIsEmptyException('The Iterable passed must contain at least one element');
        }

        $notifications = [];

        $newBuilder = new self($this->notifynderCategory);

        foreach ($dataToIterate as $key => $data) {
            $builder($newBuilder, $data, $key);
            $notifications[] = $newBuilder->toArray();
        }

        $this->notifications = $notifications;

        return $this;
    }

    /**
     * Compose the builder to
     * the array.
     *
     * @throws NotificationBuilderException
     * @return mixed
     */
    public function toArray()
    {
        $hasMultipleNotifications = $this->isMultidimensionalArray($this->notifications);

        // If the builder is handling a single notification
        // we will validate only it
        if (! $hasMultipleNotifications) {
            $this->setDate();

            if ($this->hasRequiredFields($this->notifications)) {
                return $this->notifications;
            }
        }

        // If has multiple Notifications
        // we will validate one by one
        if ($hasMultipleNotifications) {
            $allow = [];

            foreach ($this->notifications as $index => $notification) {
                $allow[$index] = $this->hasRequiredFields($notification);
            }

            if (! in_array(false, $allow)) {
                return $this->notifications;
            }
        }

        $error = 'The fields: '.implode(',', $this->getRequiredFields()).' are required';
        throw new NotificationBuilderException($error);
    }

    /**
     * Refresh the state of the notifications.
     */
    public function refresh()
    {
        $this->notifications = [];

        return $this;
    }

    /**
     * @param $var
     * @return bool
     */
    protected function isIterable($var)
    {
        return is_array($var) || $var instanceof Traversable;
    }

    /**
     * It set the entity who will do
     * the action of receive or
     * send.
     *
     * @param $from
     * @param $property
     * @return array
     */
    protected function setEntityAction($from, $property)
    {
        // Check if has the entity as parameter
        // it should be the firstOne
        if ($this->hasEntity($from)) {
            $this->isString($from[0]);
            $this->isNumeric($from[1]);

            $this->setBuilderData("{$property}_type", $from[0]);
            $this->setBuilderData("{$property}_id", $from[1]);
        } elseif ($from[0] instanceof Model) {
            $this->setBuilderData("{$property}_type", $from[0]->getMorphClass());
            $this->setBuilderData("{$property}_id", $from[0]->getKey());
        } else {
            $this->isNumeric($from[0]);
            $this->setBuilderData("{$property}_id", $from[0]);
        }
    }

    /**
     * If the values passed are 2 or more,
     * it means that you specified the entity
     * over then the id.
     *
     * @param  array $info
     * @return bool
     */
    protected function hasEntity(array $info)
    {
        return count($info) >= 2;
    }

    /**
     * Set date on the array.
     */
    protected function setDate()
    {
        $this->date = $data = Carbon::now();

        $this->setBuilderData('updated_at', $data);
        $this->setBuilderData('created_at', $data);
    }

    /**
     * @return string
     */
    protected function getDate()
    {
        return $this->date;
    }

    /**
     * Set builder Data.
     *
     * @param $field
     * @param $data
     */
    public function setBuilderData($field, $data)
    {
        return $this->notifications[$field] = $data;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->notifications);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->notifications[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (method_exists($this, $offset)) {
            return $this->{$offset}($value);
        }

        if ($this->isRequiredField($offset)) {
            $this->notifications[$offset] = $value;
        }
    }

    /**
     * @param Repository $config
     */
    public function setConfig(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetUnset($offset)
    {
        unset($this->notifications[$offset]);
    }
}
