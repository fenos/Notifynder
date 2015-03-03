<?php namespace Fenos\Notifynder\Groups;

use Fenos\Notifynder\Contracts\NotifynderGroupDB;
use Fenos\Notifynder\Models\NotificationGroup;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class NotificationGroupsRepository
 *
 * @package Fenos\Notifynder\Groups\Repositories
 */
class GroupRepository implements NotifynderGroupDB
{

    /**
     * @var NotificationGroup | Builder
     */
    protected $groupModel;

    /**
     * @param NotificationGroup $groupModel
     */
    public function __construct(NotificationGroup $groupModel)
    {
        $this->groupModel = $groupModel;
    }

    /**
     * Find a group by ID
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($group_id)
    {
        return $this->groupModel->find($group_id);
    }

    /**
     * Find a group by name
     *
     * @param $name
     * @return mixed
     */
    public function findByName($name)
    {
        return $this->groupModel->where('name', $name)->first();
    }

    /**
     * Create a new group
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($name)
    {
        return $this->groupModel->create(compact('name'));
    }

    /**
     * Delete a group
     *
     * @param $group_id
     * @return mixed
     */
    public function delete($group_id)
    {
        return  $this->groupModel->where('id', $group_id)->delete();
    }
}
