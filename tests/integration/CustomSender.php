<?php
use Fenos\Notifynder\Contracts\Sender;
use Fenos\Notifynder\Contracts\StoreNotification;

/**
 * Class CustomSender
 */
class CustomSender implements Sender
{
    /**
     * @var array
     */
    protected $notifications;

    /**
     * @var string
     */
    private $category;

    /**
     * @var \Fenos\Notifynder\NotifynderManager
     */
    private $notifynder;

    /**
     * @param array                        $notifications
     * @param null                         $category
     * @param \Fenos\Notifynder\NotifynderManager $notifynder
     */
    function __construct(array $notifications,$category = null,\Fenos\Notifynder\NotifynderManager $notifynder)
    {
        $this->notifications = $notifications;
        $this->category = $category;
        $this->notifynder = $notifynder;
    }

    /**
     * Send notification
     *
     * @param StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification)
    {
//        dd($this->notifynder);
        return $storeNotification->storeSingle($this->notifications);
    }
}