<?php

namespace Fenos\Notifynder\Builder;

use ReflectionProperty;

/**
 * Class ObjectHelper
 *
 * @package Fenos\Notifynder\Builder
 */
trait ObjectHelper {

    /**
     * Set properties of a object
     * in an array
     *
     * @param       $command
     * @return array
     */
    public function getPropertiesToArray($command)
    {
        $propertiesReflection = $this->getObjectProperties($command);

        $propertiesInArray = [];

        foreach($propertiesReflection as $property)
        {
            $nameProperty = $property->getName();

            $valuesProperty = $command->{$nameProperty};

            $propertiesInArray = $this->setPropertyArray($valuesProperty, $propertiesInArray, $nameProperty);
        }

        return $propertiesInArray;
    }

    /**
     * @param $command
     * @return \ReflectionProperty[]
     */
    public function getObjectProperties($command)
    {
        $reflectionCommand = new \ReflectionClass($command);
        $propertiesReflection = $reflectionCommand->getProperties(ReflectionProperty::IS_PROTECTED);

        return $propertiesReflection;
    }

    /**
     * Set the property in an array
     *
     * @param $valuesProperty
     * @param $properties
     * @param $nameProperty
     * @return mixed
     */
    public function setPropertyArray($valuesProperty, $properties, $nameProperty)
    {
        if (is_array($valuesProperty))
        {
            foreach ($valuesProperty as $key => $value)
            {
                $properties[$key] = $value;
            }

            return $properties;

        } else
        {
            $properties[$nameProperty] = $valuesProperty;

            return $properties;
        }
    }

} 