<?php namespace Fenos\Notifynder\Categories\Repositories;

use Fenos\Notifynder\Exceptions\CategoryNotFoundException;
use Fenos\Notifynder\Models\NotificationCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class NotifynderCategoryDB
 *
 * @package Fenos\Notifynder\Categories\Repositories
 */
class NotifynderCategoryDB implements CategoryRepository {

    /**
     * @var NotificationCategory | Builder
     */
    protected $categoryModel;

    /**
     * @param NotificationCategory $categoryModel
     */
    function __construct(NotificationCategory $categoryModel)
    {
        $this->categoryModel = $categoryModel;
    }

    /**
     * Find By Id
     *
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->categoryModel->find($id);
    }

    /**
     * Find by name
     *
     * @param $name
     * @return mixed
     */
    public function findByName($name)
    {
        return $this->categoryModel->where('name',$name)->first();
    }

    /**
     * Find by names returnig
     * lists of ids
     *
     * @param $name
     * @throws CategoryNotFoundException
     * @return mixed
     */
    public function findByNames(array $name)
    {
        return $this->categoryModel->whereIn('name',$name)->get();
    }

    /**
     * Add a category to the DB
     *
     * @param array $info
     * @return static
     */
    public function add(array $info)
    {
        return $this->categoryModel->create($info);
    }

    /**
     * Delete category by ID
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->categoryModel->where('id',$id)->delete();
    }

    /**
     * Delete category by name
     *
     * @param $name
     * @return mixed
     */
    public function deleteByName($name)
    {
        return $this->categoryModel->where('name',$name)->delete();
    }
}