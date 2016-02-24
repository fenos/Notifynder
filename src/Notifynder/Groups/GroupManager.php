<?php namespace Fenos\Notifynder\Groups;

use Fenos\Notifynder\Contracts\NotifynderGroup;
use Fenos\Notifynder\Contracts\NotifynderGroupCategoryDB;
use Fenos\Notifynder\Contracts\NotifynderGroupDB;
use Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException;
use InvalidArgumentException;

/**
 * Class NotifynderGroup
 *
 * @package Fenos\Notifynder\Groups
 */
class GroupManager implements NotifynderGroup
{

    /**
     * @var NotifynderGroupCategoryDB
     */
    protected $groupCategory;

    /**
     * @var NotifynderGroupDB
     */
    protected $groupRepo;

    /**
     * @param NotifynderGroupDB         $groupRepo
     * @param NotifynderGroupCategoryDB $groupCategory
     */
    public function __construct(NotifynderGroupDB $groupRepo,
                         NotifynderGroupCategoryDB $groupCategory)
    {
        $this->groupRepo = $groupRepo;
        $this->groupCategory = $groupCategory;
    }

    /**
     * Find a group by id
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @throws \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     */
    public function findById($group_id)
    {
        $group = $this->groupRepo->find($group_id);

        if (is_null($group)) {
            $error = "Group Not Found";
            throw new NotifynderGroupNotFoundException($error);
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
    public function findByName($group_name)
    {
        $group = $this->groupRepo->findByName($group_name);

        if (is_null($group)) {
            $error = "Group Not Found";
            throw new NotifynderGroupNotFoundException($error);
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
        return $this->groupCategory->addCategoryToGroupById($gorup_id, $category_id);
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
        return $this->groupCategory->addCategoryToGroupByName($gorup_name, $category_name);
    }

    /**
     * Add Multiple categories in a group
     * First parameter is the group name
     * all the rest are categories
     *
     * @return mixed
     */
    public function addMultipleCategoriesToGroup()
    {
        $args = func_get_args();

        // First parameter is the group name
        $group_name = array_shift($args);

        $names = (is_array($args[0])) ? $args[0] : $args;

        return $this->groupCategory->addMultipleCategoriesToGroup($group_name, $names);
    }

    /**
     * Add a group in the db
     *
     * @param $name
     * @throws InvalidArgumentException
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function addGroup($name)
    {
        if ($this->isStringWithDots($name)) {
            return $this->groupRepo->create($name);
        }

        $error = "The name must be a string with dots as namespaces";
        throw new InvalidArgumentException($error);
    }

    /**
     * Check if a string with dots
     *
     * @param $name
     * @return bool
     */
    protected function isStringWithDots($name)
    {
        return strpos($name, '.');
    }
}
