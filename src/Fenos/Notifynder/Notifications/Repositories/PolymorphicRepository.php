<?php

namespace Fenos\Notifynder\Notifications\Repositories;

trait PolymorphicRepository
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * Getter for entity property.
     *
     * @param $entity
     *
     * @return $this
     */
    public function entity($entity)
    {
        if (is_null($entity)) {
            $this->entity = false;

            return $this;
        }

        $this->entity = $entity;

        return $this;
    }

    /**
     * To keep the repository more
     * Polymorphic will be done automatically
     * with this method.
     *
     * @param $query
     * @param $table_id
     * @param $table_type
     * @param $table_id_value
     * @param $table_type_value
     *
     * @return mixed
     */
    public function wherePolymorphic($table_id, $table_type, $table_id_value, $table_type_value, $query = null)
    {
        return $this->notification->scopeWherePolymorphic(null, $table_id, $table_type, $table_id_value, $table_type_value, $query);
    }
}
