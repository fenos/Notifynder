<?php namespace Fenos\Notifynder\Categories;

use Fenos\Notifynder\Contracts\CategoryDB;
use Fenos\Notifynder\Models\NotificationCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryRepository
 *
 * Repository reponsable to approach database
 * queries of the categories
 *
 * @package Fenos\Notifynder\Categories
 */
class CategoryRepository implements CategoryDB
{

    /**
     * @var NotificationCategory | Builder
     */
    protected $categoryModel;

    /**
     * @param NotificationCategory $categoryModel
     */
    public function __construct(NotificationCategory $categoryModel)
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
        return $this->categoryModel->where('name', $name)
                    ->first();
    }

    /**
     * Find by names returnig
     * lists of ids
     *
     * @param $name
     * @return mixed
     */
    public function findByNames(array $name)
    {
        return $this->categoryModel->whereIn('name', $name)
                    ->get();
    }

    /**
     * Add a category to the DB
     *
     * @param  array  $name
     * @param         $text
     * @return static
     */
    public function add($name, $text)
    {
        return $this->categoryModel->create(compact('name', 'text'));
    }

    /**
     * Delete category by ID
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->categoryModel->where('id', $id)
                    ->delete();
    }

    /**
     * Delete category by name
     *
     * @param $name
     * @return mixed
     */
    public function deleteByName($name)
    {
        return $this->categoryModel->where('name', $name)
                    ->delete();
    }

    /**
     * Update a category by id
     *
     * @param  array $data
     * @param        $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        return $this->categoryModel->where('id', $id)
                    ->update($data);
    }
}
