<?php namespace Fenos\Notifynder\Builder;

/**
 * Class BuilderRules
 *
 * Simple trait that define the rules that
 * the builder has to match. It required mandatory
 * fields listed in the $requiredFields property
 *
 * @package Fenos\Notifynder\Builder
 */
trait BuilderRules
{

    /**
     * @var array
     */
    private $requiredFields = ['from_id','to_id','url','category_id'];

    /**
     * Value has to be a string
     *
     * @param $value
     * @return bool
     */
    protected function isString($value)
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException("The value Passed is not a string");
        }

        return true;
    }

    /**
     * Value has to be numeric
     *
     * @param $value
     * @return bool
     */
    protected function isNumeric($value)
    {
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException("The value Passed must be a number");
        }

        return true;
    }

    /**
     * Check that the builder has
     * the required field to send the
     * notifications correctly
     *
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
