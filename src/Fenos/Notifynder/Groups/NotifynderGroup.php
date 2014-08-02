<?php namespace Fenos\Notifynder\Groups;

use Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException;
use Fenos\Notifynder\Groups\Repositories\NotificationGroupCategoryRepository;
use Fenos\Notifynder\Groups\Repositories\NotificationGroupsRepository;

/**
 * Class NotifynderGroup
 *
 * @package Fenos\Notifynder\Groups
 */
class NotifynderGroup {

    /**
     * @var NotificationGroupCategoryRepository
     */
    protected $notificationPivot;

    /**
     * @var NotificationGroupsRepository
     */
    protected $groupRepo;

    /**
     * @param NotificationGroupsRepository        $groupRepo
     * @param NotificationGroupCategoryRepository $notificationPivot
     */
    function __construct(NotificationGroupsRepository $groupRepo,
                         NotificationGroupCategoryRepository$notificationPivot)
    {
        $this->groupRepo = $groupRepo;
        $this->notificationPivot = $notificationPivot;
    }

    /**
     * Find a group by id
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findGroupById($group_id)
    {
        $group = $this->groupRepo->find($group_id);

        if ( is_null($group) )
        {
            throw new NotifynderGroupNotFoundException("Group Not Found");
        }

        return $group;
    }

    /**
     * Find a group By name
     *
     * @param $group_name
     * @return mixed
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findGroupByName($group_name)
    {
        $group = $this->groupRepo->findByName($group_name);

        if ( is_null($group) )
        {
            throw new NotifynderGroupNotFoundException("Group Not Found");
        }

        return $group;
    }

    /**
     * Add category to a group
     * giving the ids of them
     *
     * @param $gorup_id
     * @param $category_id
     * @return mixed
     */
    public function addCategoryToGroupById($gorup_id, $category_id)
    {
        return $this->notificationPivot->addCategoryToGroupById($gorup_id,$category_id);
    }

    /**
     * Add category to a group
     * giving the ids of them
     *
     * @param $gorup_name
     * @param $category_name
     * @return mixed
     */
    public function addCategoryToGroupByName($gorup_name, $category_name)
    {
        return $this->notificationPivot->addCategoryToGroupByName($gorup_name,$category_name);
    }

    /**
     * Add Multiple categories in a group
     *
     * @return mixed
     */
    public function addMultipleCategoriesToGroup()
    {
        $names = func_get_args();

        $names = (is_array($names[0])) ? $names[0] : $names;

        $group_name = array_shift($names);

        $names = (is_array($names[1])) ? $names[1] : $names;

        return $this->notificationPivot->addMultipleCategoriesToGroup($group_name,$names);
    }

    /**
     * Add a group in the db
     *
     * @param $name
     * @throws \InvalidArgumentException
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function addGroup($name)
    {
        if ($this->isStringWithDots($name))
        {
            return $this->groupRepo->create($name);
        }

        throw new \InvalidArgumentException("The name must be a string with dots as namespaces");
    }

    /**
     * Check if a string with dots
     *
     * @param $name
     * @return bool
     */
    public function isStringWithDots($name)
    {
        return strpos('.',$name);
    }
} 