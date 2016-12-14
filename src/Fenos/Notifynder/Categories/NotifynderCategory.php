<?php

namespace Fenos\Notifynder\Categories;

use Fenos\Notifynder\Categories\Repositories\CategoryRepository;
use Fenos\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class NotifynderCategory.
 */
class NotifynderCategory
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepo;

    /**
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Find by name.
     *
     * @param $name
     *
     * @throws CategoryNotFoundException
     *
     * @return mixed
     */
    public function findByName($name)
    {
        $category = $this->categoryRepo->findByName($name);

        if (is_null($category)) {
            throw new CategoryNotFoundException('Category Not Found');
        }

        return $category;
    }

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
    public function findByNames(array $name)
    {
        $category = $this->categoryRepo->findByNames($name);

        if (count($category) == 0) {
            throw new CategoryNotFoundException('Category Not Found');
        }

        return $category;
    }

    /**
     * Find By Id.
     *
     * @param $id
     *
     * @throws CategoryNotFoundException
     *
     * @return mixed
     */
    public function find($id)
    {
        $category = $this->categoryRepo->find($id);

        if (is_null($category)) {
            throw new CategoryNotFoundException('Category Not Found');
        }

        return $category;
    }

    /**
     * Add a category to the DB.
     *
     * @param array $info
     *
     * @return \Fenos\Notifynder\Models\NotificationCategory
     */
    public function add(array $info)
    {
        return $this->categoryRepo->add($info);
    }

    /**
     * Delete category by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->categoryRepo->delete($id);
    }

    /**
     * Delete category by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function deleteByName($name)
    {
        return $this->categoryRepo->deleteByName($name);
    }
}
