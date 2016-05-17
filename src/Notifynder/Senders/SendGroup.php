<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\NotifynderCategory;
use Fenos\Notifynder\Contracts\NotifynderGroup;
use Fenos\Notifynder\Contracts\DefaultSender;
use Fenos\Notifynder\Contracts\StoreNotification;
use Fenos\Notifynder\NotifynderManager;

/**
 * Class SendGroup.
 */
class SendGroup implements DefaultSender
{
    /**
     * @var NotifynderManager
     */
    protected $notifynder;

    /**
     * @var string
     */
    protected $nameGroup;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var NotifynderCategory
     */
    protected $notifynderCategory;

    /**
     * @param NotifynderGroup    $notifynderGroup
     * @param NotifynderCategory $notifynderCategory
     * @param string             $nameGroup
     * @param array | \Closure   $info
     */
    public function __construct(NotifynderGroup $notifynderGroup,
                         NotifynderCategory $notifynderCategory,
                         $nameGroup,
                         array $info)
    {
        $this->info = $info;
        $this->nameGroup = $nameGroup;
        $this->notifynderGroup = $notifynderGroup;
        $this->notifynderCategory = $notifynderCategory;
    }

    /**
     * Send group notifications.
     *
     * @param  StoreNotification $sender
     * @return mixed
     */
    public function send(StoreNotification $sender)
    {
        // Get group
        $group = $this->notifynderGroup->findByName($this->nameGroup);

        // Categories
        $categoriesAssociated = $group->categories;

        // Send a notification for each category
        foreach ($categoriesAssociated as $category) {
            // Category name
            $categoryModel = $this->notifynderCategory->findByName($category->name);

            $notification = array_merge(
                ['category_id' => $categoryModel->id],
                $this->info
            );

            $sender->storeSingle($notification);
        }

        return $group;
    }
}
