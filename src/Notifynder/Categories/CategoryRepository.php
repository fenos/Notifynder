<?php

namespace Fenos\Notifynder\Categories;

use Fenos\Notifynder\Contracts\CategoryDB;
use Fenos\Notifynder\Models\NotificationCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryRepository.
 *
 * Repository responsible to approach database
 * queries of the categories
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
     * Find By Id.
     *
     * @param $categoryId
     * @return mixed
     */
    public function find($categoryId)
    {
        return $this->categoryModel->find($categoryId);
    }

    /**
     * Find by name.
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
     * Find by names returning
     * lists of ids.
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
     * Add a category to the DB.
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
     * Delete category by ID.
     *
     * @param $categoryId
     * @return mixed
     */
    public function delete($categoryId)
    {
        return $this->categoryModel->where('id', $categoryId)
                    ->delete();
    }

    /**
     * Delete category by name.
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
     * Update a category by id.
     *
     * @param  array $data
     * @param        $categoryId
     * @return mixed
     */
    public function update(array $data, $categoryId)
    {
        return $this->categoryModel->where('id', $categoryId)
                    ->update($data);
    }
}
