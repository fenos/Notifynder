<?php namespace Fenos\Notifynder\Groups;

use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Contracts\NotifynderGroupCategoryDB;
use Fenos\Notifynder\Models\NotificationCategory;
use Fenos\Notifynder\Models\NotificationGroup;

/**
 * Class NotificationGroupCategoryRepository
 *
 * @package Fenos\Notifynder\Groups\Repositories
 */
class GroupCategoryRepository implements NotifynderGroupCategoryDB
{

    /**
     * @var NotificationGroup
     */
    protected $notificationGropup;

    /**
     * @var NotificationCategory
     */
    protected $notificationCategory;

    /**
     * @param NotifynderCategory $notificationCategory
     * @param NotificationGroup  $notificationGropup
     */
    public function __construct(NotifynderCategory $notificationCategory,
                         NotificationGroup $notificationGropup)
    {
        $this->notificationCategory = $notificationCategory;
        $this->notificationGropup = $notificationGropup;
    }

    /**
     * Add a category in a group
     *
     * @param                                                                                      $group_id
     * @param                                                                                      $category_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function addCategoryToGroupById($group_id, $category_id)
    {
        $group = $this->notificationGropup->find($group_id);
        $group->categories()->attach($category_id);

        return $group;
    }

    /**
     * Add a category in a group
     * by names given
     *
     * @param $group_name
     * @param $category_name
     * @return mixed
     */
    public function addCategoryToGroupByName($group_name, $category_name)
    {
        $group = $this->notificationGropup->where('name', $group_name)->first();

        $category = $this->notificationCategory->findByName($category_name);

        $group->categories()->attach($category->id);

        return $group;
    }

    /**
     * Add multiple categories by them names
     * to a group
     *
     * @param $group_name
     * @param $names
     * @return mixed
     */
    public function addMultipleCategoriesToGroup($group_name, array $names)
    {
        $group = $this->notificationGropup->where('name', $group_name)->first();

        $categories = $this->notificationCategory->findByNames($names);

        foreach ($categories as $category) {
            $group->categories()->attach($category->id);
        }

        return $group;
    }
}
