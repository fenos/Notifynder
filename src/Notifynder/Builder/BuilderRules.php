<?php

namespace Fenos\Notifynder\Builder;

use Illuminate\Contracts\Config\Repository;
use InvalidArgumentException;
use Carbon\Carbon;

/**
 * Class BuilderRules.
 *
 * Simple trait that define the rules that
 * the builder has to match. It required mandatory
 * fields listed in the $requiredFields property
 */
trait BuilderRules
{
    /**
     * @var array
     */
    private $requiredFields = ['from_id', 'to_id', 'category_id'];

    /**
     * Value has to be a string.
     *
     * @param $value
     * @return bool
     */
    protected function isString($value)
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException('The value Passed is not a string');
        }

        return true;
    }

    /**
     * Value has to be a valid Carbon Instance.
     *
     * @param $value
     * @return bool | InvalidArgumentException
     */
    protected function isCarbon($value)
    {
        if ($value instanceof Carbon) {
            return true;
        }

        throw new InvalidArgumentException('The value Passed has to be an instance of Carbon\\Carbon');
    }

    /**
     * Value has to be numeric.
     *
     * @param $value
     * @return bool
     */
    protected function isNumeric($value)
    {
        if (! is_numeric($value)) {
            throw new InvalidArgumentException('The value Passed must be a number');
        }

        return true;
    }

    /**
     * Returns all required fields including the config ones.
     *
     * @return array
     */
    public function getRequiredFields()
    {
        $customRequiredFields = [];
        if (property_exists($this, 'config') && $this->config instanceof Repository) {
            $customRequiredFields = $this->config->get('notifynder.additional_fields.required', []);
        }

        return array_unique($this->requiredFields + $customRequiredFields);
    }

    /**
     * Check that the builder has
     * the required field to send the
     * notifications correctly.
     *
     * @param $array
     * @return bool
     */
    public function hasRequiredFields($array)
    {
        foreach ($this->getRequiredFields() as $field) {
            if (! array_key_exists($field, $array)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if is a required field.
     *
     * @param $offset
     * @return bool
     */
    public function isRequiredField($offset)
    {
        return in_array($offset, $this->getRequiredFields());
    }

    /**
     * Check if the array passed is
     * multidimensional.
     *
     * @param $arr
     * @return bool
     */
    protected function isReadyArrToFormatInJson(array $arr)
    {
        if ($this->isAssociativeArr($arr)) {
            return true;
        }

        if (count($arr) > 0) {
            $error = "The 'extra' value must to be an associative array";
            throw new InvalidArgumentException($error);
        }

        return false;
    }

    /**
     * @param array $arr
     * @return bool
     */
    protected function isAssociativeArr(array $arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Check if the array is
     * multidimensional.
     *
     * @param $arr
     * @return bool
     */
    public function isMultidimensionalArray($arr)
    {
        $rv = array_filter($arr, 'is_array');
        if (count($rv) > 0) {
            return true;
        }

        return false;
    }
}
