<?php

namespace Fenos\Notifynder\Builder;

use Carbon\Carbon;
use Fenos\Notifynder\Categories\NotifynderCategory;
use Fenos\Notifynder\Exceptions\NotificationBuilderException;
use Traversable;

/**
 * Class NotifynderBuilder.
 */
class NotifynderBuilder
{
    use BuilderRules,ObjectHelper;

    /**
     * @var string | array
     */
    protected $from;

    /**
     * @var string | array
     */
    protected $to;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string | int
     */
    protected $category;

    /**
     * @var mixed
     */
    protected $extra;

    /**
     * @var Carbon
     */
    protected $created_at;

    /**
     * @var Carbon
     */
    protected $updated_at;

    /**
     * @var NotifynderCategory
     */
    private $notifynderCategory;

    public function __construct(NotifynderCategory $notifynderCategory)
    {
        $this->notifynderCategory = $notifynderCategory;
    }

    /**
     * Compose the builder to
     * the array.
     *
     * @throws NotificationBuilderException
     *
     * @return mixed
     */
    public function getArray()
    {
        $this->setDate();

        $builtArray = $this->getPropertiesToArray($this);

        if ($this->hasRequiredFields($builtArray)) {
            return $builtArray;
        }

        $error = "The fields:  'from_id' , 'to_id', 'url', 'category_id' are required";
        throw new NotificationBuilderException($error);
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
     *
     * @return $this
     */
    public function url($url)
    {
        $this->isString($url);

        $this->url = ['url' => $url];

        return $this;
    }

    /**
     * Set Category and covert it, to the id
     * if name of it given.
     *
     * @param $category
     *
     * @return $this
     */
    public function category($category)
    {
        if (is_string($category)) {
            $category = $this->notifynderCategory->findByName($category)->id();
        }

        $this->category = ['category_id' => $category];

        return $this;
    }

    /**
     * Build the array with the builder inside
     * a Closure, it has more flexibility for
     * the generation of your array.
     *
     *
     * @param callable $closure
     *
     * @return array | false
     */
    public function raw(\Closure $closure)
    {
        $builder = $closure($this);

        if (!is_null($builder)) {
            return $this->getArray();
        }

        return false;
    }

    /**
     * Set extra value.
     *
     * @param $extra
     *
     * @return $this
     */
    public function extra($extra)
    {
        $this->isString($extra);

        $this->extra = $extra;

        return $this;
    }

    /**
     * Loop the datas for create
     * multi notifications array.
     *
     * @param          $dataToIterate
     * @param callable $builder
     *
     * @return $this
     */
    public function loop($dataToIterate, \Closure $builder)
    {
        if ($this->isIterable($dataToIterate)) {
            $arrayOfData = [];

            foreach ($dataToIterate as $key => $data) {
                $dataBuilt = $builder($this, $key, $data);

                if ($dataBuilt) {
                    $arrayOfData[] = $this->getArray();
                }
            }

            return $arrayOfData;
        }

        throw new \InvalidArgumentException('The data passed must be itarable');
    }

    /**
     * @param $var
     *
     * @return bool
     */
    public function isIterable($var)
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
     *
     * @return array
     */
    public function setEntityAction($from, $property)
    {
        // Check if has the entity as parameter
        // it should be the firstOne
        if ($this->hasEntity($from)) {
            $this->isString($from[0]);
            $this->isNumeric($from[1]);

            return $this->{$property} = ["{$property}_type" => $from[0], "{$property}_id" => $from[1]];
        } else {
            $this->isNumeric($from[0]);

            return $this->{$property} = ["{$property}_id" => $from[0]];
        }
    }

    /**
     * If the values passed are 2 or more,
     * it means that you spefied the entity
     * over then the id.
     *
     * @param array $info
     *
     * @return bool
     */
    public function hasEntity(array $info)
    {
        return count($info) >= 2;
    }

    /**
     * Set date on the array.
     */
    public function setDate()
    {
        $this->updated_at = Carbon::now();
        $this->created_at = Carbon::now();
    }
}
