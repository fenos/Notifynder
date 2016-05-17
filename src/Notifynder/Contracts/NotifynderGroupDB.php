<?php

namespace Fenos\Notifynder\Contracts;

/**
 * Class NotificationGroupsRepository.
 */
interface NotifynderGroupDB
{
    /**
     * Find a group by ID.
     *
     * @param $groupId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($groupId);

    /**
     * Find a group by name.
     *
     * @param $name
     * @return mixed
     */
    public function findByName($name);

    /**
     * Create a new group.
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function create($name);

    /**
     * Delete a group.
     *
     * @param $groupId
     * @return mixed
     */
    public function delete($groupId);
}
