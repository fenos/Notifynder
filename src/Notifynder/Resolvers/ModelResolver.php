<?php

namespace Fenos\Notifynder\Resolvers;

use Illuminate\Support\Str;

class ModelResolver
{
    protected $models = [];
    protected $tables = [];

    public function setModel($class, $model)
    {
        $this->models[$class] = $model;
    }

    public function setTable($class, $table)
    {
        $this->tables[$class] = $table;
    }

    public function getModel($class)
    {
        if (isset($this->models[$class])) {
            return $this->models[$class];
        }

        return $class;
    }

    public function getTable($class)
    {
        if (isset($this->tables[$class])) {
            return $this->tables[$class];
        }

        return str_replace('\\', '', Str::snake(Str::plural(class_basename($this->getModel($class)))));
    }

    public function make($class, array $attributes = [])
    {
        $model = $this->getModel($class);
        if (! class_exists($model)) {
            throw new \ReflectionException("Class {$model} does not exist");
        }

        return new $model($attributes);
    }
}
