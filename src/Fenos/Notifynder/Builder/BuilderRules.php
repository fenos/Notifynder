<?php namespace Fenos\Notifynder\Builder;

trait BuilderRules
{

    /**
     * @var array
     */
    private $requiredFields = ['from_id','to_id','url','category_id'];

    /**
     * Must be a string
     *
     * @param $value
     * @return bool
     */
    public function isString($value)
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException("The value Passed is not a string");
        }

        return true;
    }

    /**
     * Must Be numeric
     *
     * @param $value
     * @return bool
     */
    public function isNumeric($value)
    {
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException("The value Passed must be a number");
        }

        return true;
    }

    /**
     * @param $array
     * @return bool
     */
    public function hasRequiredFields($array)
    {
        foreach ($this->requiredFields as $field) {
            if (! array_key_exists($field, $array)) {
                return false;
            }
        }

        return true;
    }
}
