<?php
/**
 * Created by Fabrizio Fenoglio.
 */

namespace Fenos\Notifynder\Categories\Repositories;

use Fenos\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class NotifynderCategoryDB.
 */
interface CategoryRepository
{
    /**
     * Delete category by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);

    /**
     * Find by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function findByName($name);

    /**
     * Find By Id.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id);

    /**
     * Delete category by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function deleteByName($name);

    /**
     * Add a category to the DB.
     *
     * @param array $info
     *
     * @return \Fenos\Notifynder\Models\NotificationCategory
     */
    public function add(array $info);

    /**
     * Find by names returnig
     * lists of ids.
     *
     * @param $name
     *
     * @throws CategoryNotFoundException
     *
     * @return mixed
     */
    public function findByNames(array $name);
}
