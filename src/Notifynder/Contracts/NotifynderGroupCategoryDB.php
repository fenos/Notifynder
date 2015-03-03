<?php namespace Fenos\Notifynder\Contracts;

/**
 * Class NotificationGroupCategoryRepository
 *
 * @package Fenos\Notifynder\Groups\Repositories
 */
interface NotifynderGroupCategoryDB
{

    /**
     * Add a category in a group
     *
     * @param                                                                                      $group_id
     * @param                                                                                      $category_id
     * @internal param \Fenos\Notifynder\Models\NotificationCategory $category
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function addCategoryToGroupById($group_id, $category_id);

    /**
     * Add a category in a group
     * by names given
     *
     * @param $group_name
     * @param $category_name
     * @return mixed
     */
    public function addCategoryToGroupByName($group_name, $category_name);

    /**
     * Add multiple categories by them names
     * to a group
     *
     * @param $group_name
     * @param $names
     * @return mixed
     */
    public function addMultipleCategoriesToGroup($group_name, array $names);
}
