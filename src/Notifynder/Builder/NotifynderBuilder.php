<?php namespace Fenos\Notifynder\Builder;

use ArrayAccess;
use Carbon\Carbon;
use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Exceptions\NotificationBuilderException;
use InvalidArgumentException;
use Traversable;
use Closure;

/**
 * Class NotifynderBuilder
 *
 * The builder is responsable to create with a elegant and easy
 * syntax your notifications arrays. It provide values validations
 * to keep clean of your data. Also it will add the "sugar" to
 * multi notifications as created_at and updated_at field automatically
 *
 * @package Fenos\Notifynder\Builder
 */
class NotifynderBuilder implements ArrayAccess
{
    use BuilderRules;

    /**
     * @var string notification to store
     */
    public $date;

    /**
     * Builder data
     *
     * @var array
     */
    protected $builder = [];

    /**
     * @var NotifynderCategory
     */
    private $notifynderCategory;

    /**
     * @param NotifynderCategory $notifynderCategory
     */
    function __construct(NotifynderCategory $notifynderCategory)
    {
        $this->notifynderCategory = $notifynderCategory;
    }

    /**
     * Set who will send the notification
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
     * Set who will receive the notification
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
     * Set the url of the notification
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
     * Set Category and covert it, to the id
     * if name of it given
     *
     * @param $category
     * @return $this
     */
    public function category($category)
    {
        if (!is_numeric($category)) {
            $category = $this->notifynderCategory
                            ->findByName($category)->id;
        }

        $this->setBuilderData('category_id', $category);

        return $this;
    }

    /**
     * Set extra value
     *
     * @param $extra
     * @return $this
     */
    public function extra($extra)
    {
        $this->isString($extra);

        $this->setBuilderData('extra', $extra);

        return $this;
    }

    /**
     * Build the array with the builder inside
     * a Closure, it has more flexibility for
     * the generation of your array
     *
     *
     * @param  callable $closure
     * @return array    | false
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
     * Loop the datas for create
     * multi notifications array
     *
     * @param           $dataToIterate
     * @param  callable $builder
     * @return $this
     */
    public function loop($dataToIterate, \Closure $builder)
    {
        if ($this->isIterable($dataToIterate)) {
            $arrayOfData = [];

            foreach ($dataToIterate as $key => $data) {
                $dataBuilt = $builder($this, $data, $key);

                if ($dataBuilt) {
                    $arrayOfData[] = $this->toArray();
                }
            }

            return $arrayOfData;
        }

        $error = "The data passed must be itarable";
        throw new InvalidArgumentException($error);
    }

    /**
     * Compose the builder to
     * the array
     *
     * @throws NotificationBuilderException
     * @return mixed
     */
    public function toArray()
    {
        $this->setDate();

        if ($this->hasRequiredFields($this->builder)) {
            return $this->builder;
        }

        $error = "The fields:  'from_id' , 'to_id', 'url', 'category_id' are required";
        throw new NotificationBuilderException($error);
    }

    /**
     * @param $var
     * @return bool
     */
    protected function isIterable($var)
    {
        return (is_array($var) || $var instanceof Traversable);
    }

    /**
     * It set the entity who will do
     * the action of receive or
     * send
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
        } else {
            $this->isNumeric($from[0]);
            $this->setBuilderData("{$property}_id", $from[0]);
        }
    }

    /**
     * If the values passed are 2 or more,
     * it means that you spefied the entity
     * over then the id
     *
     * @param  array $info
     * @return bool
     */
    protected function hasEntity(array $info)
    {
        return count($info) >= 2;
    }

    /**
     * Set date on the array
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
     * Set builder Data
     *
     * @param $field
     * @param $data
     */
    protected function setBuilderData($field, $data)
    {
        return $this->builder[$field] = $data;
    }


    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset,$this->builder);
    }


    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->builder[$offset];
    }


    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        unset($this->builder[$offset]);
    }


    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetUnset($offset)
    {
        return isset($this->builder[$offset]) ? $this->builder[$offset] : null;
}}
