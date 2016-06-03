<?php

namespace Fenos\Notifynder\Contracts;

/**
 * Class NotificationGroupCategoryRepository.
 */
interface NotifynderGroupCategoryDB
{
    /**
     * Add a category in a group.
     *
     * @param  $groupId
     * @param  $categoryId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function addCategoryToGroupById($groupId, $categoryId);

    /**
     * Add a category in a group
     * by names given.
     *
     * @param $groupName
     * @param $categoryName
     * @return mixed
     */
    public function addCategoryToGroupByName($groupName, $categoryName);

    /**
     * Add multiple categories by them names
     * to a group.
     *
     * @param $groupName
     * @param $names
     * @return mixed
     */
    public function addMultipleCategoriesToGroup($groupName, array $names);
}
