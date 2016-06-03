<?php

namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class CategoryManager.
 *
 * The CategoryManager is responsible to deal
 * with the notification categories
 */
interface NotifynderCategory
{
    /**
     * Find a category by name.
     *
     * @param $name
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function findByName($name);

    /**
     * Find categories by names,
     * pass the name as an array.
     *
     * @param $name
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function findByNames(array $name);

    /**
     * Find a category by id.
     *
     * @param $categoryId
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function find($categoryId);

    /**
     * Add a category to the DB.
     *
     * @param $name
     * @param $text
     * @return \Fenos\Notifynder\Models\NotificationCategory
     */
    public function add($name, $text);

    /**
     * Delete category by ID.
     *
     * @param $categoryId
     * @return mixed
     */
    public function delete($categoryId);

    /**
     * Delete category by name.
     *
     * @param $name
     * @return mixed
     */
    public function deleteByName($name);

    /**
     * Update a category.
     *
     * @param  array $data
     * @param        $categoryId
     * @return mixed
     */
    public function update(array $data, $categoryId);
}
