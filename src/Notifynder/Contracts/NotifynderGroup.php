<?php namespace Fenos\Notifynder\Contracts;

use InvalidArgumentException;

/**
 * Class NotifynderGroup
 *
 * @package Fenos\Notifynder\Groups
 */
interface NotifynderGroup
{

    /**
     * Find a group by id
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findById($group_id);

    /**
     * Find a group By name
     *
     * @param $group_name
     * @return mixed
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findByName($group_name);

    /**
     * Add category to a group
     * giving the ids of them
     *
     * @param $gorup_id
     * @param $category_id
     * @return mixed
     */
    public function addCategoryToGroupById($gorup_id, $category_id);

    /**
     * Add category to a group
     * giving the ids of them
     *
     * @param $gorup_name
     * @param $category_name
     * @return mixed
     */
    public function addCategoryToGroupByName($gorup_name, $category_name);

    /**
     * Add Multiple categories in a group
     * First parameter is the group name
     * all the rest are categories
     *
     * @return mixed
     */
    public function addMultipleCategoriesToGroup();

    /**
     * Add a group in the db
     *
     * @param $name
     * @throws InvalidArgumentException
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function addGroup($name);
}
