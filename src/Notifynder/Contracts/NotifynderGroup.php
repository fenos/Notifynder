<?php

namespace Fenos\Notifynder\Contracts;

use InvalidArgumentException;

/**
 * Class NotifynderGroup.
 */
interface NotifynderGroup
{
    /**
     * Find a group by id.
     *
     * @param $groupId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findById($groupId);

    /**
     * Find a group By name.
     *
     * @param $groupName
     * @return mixed
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findByName($groupName);

    /**
     * Add category to a group
     * giving the ids of them.
     *
     * @param $groupId
     * @param $categoryId
     * @return mixed
     */
    public function addCategoryToGroupById($groupId, $categoryId);

    /**
     * Add category to a group
     * giving the ids of them.
     *
     * @param $groupName
     * @param $categoryName
     * @return mixed
     */
    public function addCategoryToGroupByName($groupName, $categoryName);

    /**
     * Add Multiple categories in a group
     * First parameter is the group name
     * all the rest are categories.
     *
     * @return mixed
     */
    public function addMultipleCategoriesToGroup();

    /**
     * Add a group in the db.
     *
     * @param $name
     * @throws InvalidArgumentException
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function addGroup($name);
}
