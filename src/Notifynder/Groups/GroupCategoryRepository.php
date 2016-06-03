<?php

namespace Fenos\Notifynder\Groups;

use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Contracts\NotifynderGroupCategoryDB;
use Fenos\Notifynder\Models\NotificationCategory;
use Fenos\Notifynder\Models\NotificationGroup;

/**
 * Class NotificationGroupCategoryRepository.
 */
class GroupCategoryRepository implements NotifynderGroupCategoryDB
{
    /**
     * @var NotificationGroup
     */
    protected $notificationGroup;

    /**
     * @var NotificationCategory
     */
    protected $notificationCategory;

    /**
     * @param NotifynderCategory $notificationCategory
     * @param NotificationGroup  $notificationGroup
     */
    public function __construct(NotifynderCategory $notificationCategory,
                         NotificationGroup $notificationGroup)
    {
        $this->notificationCategory = $notificationCategory;
        $this->notificationGroup = $notificationGroup;
    }

    /**
     * Add a category in a group.
     *
     * @param  $groupId
     * @param  $categoryId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function addCategoryToGroupById($groupId, $categoryId)
    {
        $group = $this->notificationGroup->find($groupId);
        $group->categories()->attach($categoryId);

        return $group;
    }

    /**
     * Add a category in a group
     * by names given.
     *
     * @param $groupName
     * @param $categoryName
     * @return mixed
     */
    public function addCategoryToGroupByName($groupName, $categoryName)
    {
        $group = $this->notificationGroup->where('name', $groupName)->first();

        $category = $this->notificationCategory->findByName($categoryName);

        $group->categories()->attach($category->id);

        return $group;
    }

    /**
     * Add multiple categories by them names
     * to a group.
     *
     * @param $groupName
     * @param $names
     * @return mixed
     */
    public function addMultipleCategoriesToGroup($groupName, array $names)
    {
        $group = $this->notificationGroup->where('name', $groupName)->first();

        $categories = $this->notificationCategory->findByNames($names);

        foreach ($categories as $category) {
            $group->categories()->attach($category->id);
        }

        return $group;
    }
}
