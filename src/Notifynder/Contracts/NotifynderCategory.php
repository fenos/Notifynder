<?php namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class CategoryManager
 *
 * The CategoryManager is responsable to deal
 * with the notification categories
 *
 * @package Fenos\Notifynder\Categories
 */
interface NotifynderCategory
{

    /**
     * Find a category by name
     *
     * @param $name
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function findByName($name);

    /**
     * Find categories by names,
     * pass the name as an array
     *
     * @param $name
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function findByNames(array $name);

    /**
     * Find a category by id
     *
     * @param $id
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function find($id);

    /**
     * Add a category to the DB
     *
     * @param $name
     * @param $text
     * @return \Fenos\Notifynder\Models\NotificationCategory
     */
    public function add($name, $text);

    /**
     * Delete category by ID
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Delete category by name
     *
     * @param $name
     * @return mixed
     */
    public function deleteByName($name);

    /**
     * Update a category
     *
     * @param  array $data
     * @param        $id
     * @return mixed
     */
    public function update(array $data, $id);
}
