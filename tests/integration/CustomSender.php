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
     * @var \Fenos\Notifynder\NotifynderManager
     */
    private $notifynder;

    /**
     * @param array                        $notifications
     * @param \Fenos\Notifynder\NotifynderManager $notifynder
     */
    function __construct(array $notifications,\Fenos\Notifynder\NotifynderManager $notifynder)
    {
        $this->notifications = $notifications;
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
//        dd($storeNotification);
        return $storeNotification->storeSingle($this->notifications);
    }
}