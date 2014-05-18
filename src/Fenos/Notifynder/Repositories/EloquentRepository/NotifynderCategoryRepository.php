<?php namespace Fenos\Notifynder\Repositories\EloquentRepository;

use Fenos\Notifynder\Models\NotificationCategory;
use Illuminate\Database\DatabaseManager as DB;

//Exceptions
use Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException;

/**
*
*/
class NotifynderCategoryRepository implements NotifynderCategoryEloquentRepositoryInterface
{
    /**
    * @var \Fenos\Notifynder\Models\NotificationCategory (Eloquent)
    */
    protected $notifynderType;

    function __construct(NotificationCategory $notifynderType, DB $query)
    {
        $this->notifynderType = $notifynderType;
        $this->query = $query;
    }

    /**
     * Find category notification by id
     *
     * @param $notifynderCategoryId (int)
     * @throws \Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException
     * @return \Fenos\Notifynder\Models\NotificationCategory
     */
    public function find($notifynderCategoryId)
    {
        $notificationCategoryInfo = $this->notifynderType->find($notifynderCategoryId);

        if ( !is_null($notificationCategoryInfo) )
        {
            return $notificationCategoryInfo;
        }

        throw new NotificationCategoryNotFoundException("Notification type not found");

    }

    /**
    * Get id category by name given
    *
    * @param $name (String)
    * @return \Fenos\Notifynder\Models\NotificationCategory
    */
    public function findByName($name)
    {
        return $this->notifynderType->where('name',$name)->first();
    }

    /**
    * Add notification category to the db
    *
    * @param $name     (String)
    * @param $text     (String)
    * @return \Fenos\Notifynder\Models\NotificationCategory
    */
    public function add($name, $text)
    {
        return $this->notifynderType->create(

            [
                'name'     => $name,
                'text'    => $text
            ]
        );
    }

    /**
    * Delete type notification from database
    *
    * @param $id     (int)
    * @return Boolean
    */
    public function delete($id)
    {
        $notificationType = $this->notifynderType->find($id);
        return $notificationType->delete();
    }

    /**
     * Update current category notification
     *
     * @param array $informations (Array)
     * @param $id (int)
     * @throws \Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException
     * @return \Fenos\Notifynder\Models\NotificationCategory
     */
    public function update(array $informations,$id)
    {
        $notificationType = $this->notifynderType->find($id);

        if (!is_null( $notificationType ) )
        {
            $notificationType->name = (is_null($informations['name'])) ? $notificationType->name : $informations['name'];
            $notificationType->text = (is_null($informations['text'])) ? $notificationType->text : $informations['text'];
            return $notificationType->save();
        }

        throw new NotificationCategoryNotFoundException("Notification type not found");
    }
}
