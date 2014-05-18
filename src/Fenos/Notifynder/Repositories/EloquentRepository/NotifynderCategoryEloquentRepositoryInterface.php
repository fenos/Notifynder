<?php namespace Fenos\Notifynder\Repositories\EloquentRepository;

interface NotifynderCategoryEloquentRepositoryInterface
{
    /**
    * Find category notification by id
    *
    * @param $notifynderCategoryId    (int)
    * @return \Fenos\Notifynder\Models\NotificationCategory | NotificationCategoryNotFoundException
    */
    public function find($notifynderCategoryId);

    /**
    * Get id category by name given
    *
    * @param $name (String)
    * @return \Fenos\Notifynder\Models\NotificationCategory
    */
    public function findByName($name);

    /**
    * Add notification type to the db
    *
    * @param $name     (String)
    * @param $text     (String)
    * @return \Fenos\Notifynder\Models\NotificationCategory
    */
    public function add($name, $text);

    /**
    * Delete type notification from database
    *
    * @param $name     (String)
    * @return Boolean
    */
    public function delete($name);

    /**
    * Update current type notification
    *
    * @param $name     (String)
    * @param $informations (Array)
    * @return \Fenos\Notifynder\Models\NotificationCategory
    */
    public function update(array $informations,$name);
}
