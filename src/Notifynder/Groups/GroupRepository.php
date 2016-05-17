<?php

namespace Fenos\Notifynder\Groups;

use Fenos\Notifynder\Contracts\NotifynderGroupDB;
use Fenos\Notifynder\Models\NotificationGroup;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class NotificationGroupsRepository.
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
     * Find a group by ID.
     *
     * @param $groupId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($groupId)
    {
        return $this->groupModel->find($groupId);
    }

    /**
     * Find a group by name.
     *
     * @param $name
     * @return mixed
     */
    public function findByName($name)
    {
        return $this->groupModel->where('name', $name)->first();
    }

    /**
     * Create a new group.
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($name)
    {
        return $this->groupModel->create(compact('name'));
    }

    /**
     * Delete a group.
     *
     * @param $groupId
     * @return mixed
     */
    public function delete($groupId)
    {
        return  $this->groupModel->where('id', $groupId)->delete();
    }
}
