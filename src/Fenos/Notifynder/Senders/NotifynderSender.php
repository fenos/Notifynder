<?php namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Notifications\NotifynderNotification;
use Fenos\Notifynder\Notifications\Repositories\NotificationRepository;
use Fenos\Notifynder\Notifynder;
use Fenos\Notifynder\Senders\Queue\NotifynderQueue;
use Illuminate\Config\Repository;
use Illuminate\Queue\QueueManager;

/**
 * Class NotifynderSender
 *
 * @package Fenos\Notifynder\Senders
 */
class NotifynderSender {

    /**
     * @var NotifynderSenderFactory
     */
    protected $senderFactory;

    /**
     * @var NotificationRepository
     */
    protected $notification;

    /**
     * @var Queue\NotifynderQueue
     */
    private $notifynderQueue;

    /**
     * @param NotifynderSenderFactory $senderFactory
     * @param NotifynderNotification  $notification
     * @param NotifynderQueue         $notifynderQueue
     */
    function __construct(NotifynderSenderFactory $senderFactory,
                         NotifynderNotification $notification,
                         NotifynderQueue $notifynderQueue)
    {
        $this->senderFactory = $senderFactory;
        $this->notification = $notification;
        $this->notifynderQueue = $notifynderQueue;
    }

    /**
     * Delegate the notification to store
     * on the DB
     *
     * @param array $info
     * @param null  $category
     * @return mixed
     */
    public function send(array $info, $category = null)
    {
        if ($this->notifynderQueue->isActive())
        {
            return $this->notifynderQueue->push(['info' => $info, 'category' => $category]);
        }

        return $this->sendNow($info, $category);
    }

    /**
     * Send now whichever data passed
     *
     * @param array $info
     * @param       $category
     * @return mixed
     */
    public function sendNow(array $info, $category = null)
    {
        $sender = $this->senderFactory->getSender($info, $category);

        return $sender->send($this->notification);
    }

    /**
     * Send one method to get fully working
     * older version
     *
     * @param $info
     * @param $category
     * @return SendOne
     */
    public function sendOne(array $info, $category = null)
    {
        return $this->senderFactory->sendSingle($info,$category)
            ->send($this->notification,$category);
    }

    /**
     * Send Multiple method to get fully working
     * older version
     *
     * @param $info
     * @return SendMultiple
     */
    public function sendMultiple(array $info)
    {
        return $this->senderFactory->sendMultiple($info)->send($this->notification);
    }

    /**
     * Send a group of notifications
     * at once
     *
     * @param Notifynder    $notifynder
     * @param               $group_name
     * @param array         $info
     * @return mixed
     */
    public function sendGroup(Notifynder $notifynder, $group_name, $info = [])
    {
        return $this->senderFactory->sendGroup(
            $notifynder,
            $group_name,
            $info
        )->send($this->notification);

    }
}