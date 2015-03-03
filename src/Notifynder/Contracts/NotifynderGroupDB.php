<?php namespace Fenos\Notifynder\Contracts;

/**
 * Class NotificationGroupsRepository
 *
 * @package Fenos\Notifynder\Groups\Repositories
 */
interface NotifynderGroupDB
{

    /**
     * Find a group by ID
     *
     * @param $group_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     */
    public function find($group_id);

    /**
     * Find a group by name
     *
     * @param $name
     * @return mixed
     */
    public function findByName($name);

    /**
     * Create a new group
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function create($name);

    /**
     * Delete a group
     *
     * @param $group_id
     * @return mixed
     */
    public function delete($group_id);
}
