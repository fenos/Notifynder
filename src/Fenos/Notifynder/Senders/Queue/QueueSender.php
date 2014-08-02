<?php namespace Fenos\Notifynder\Senders\Queue;

/**
 * Class QueueSender
 *
 * @package Fenos\Notifynder\Senders
 */
class QueueSender {

    /**
     * @var NotifynderSender
     */
    protected $notifynderSender;

    /**
     * @param NotifynderSender $notifynderSender
     */
    function __construct(NotifynderSender $notifynderSender)
    {
        $this->notifynderSender = $notifynderSender;
    }

    /**
     * Put in a queue the notification to send
     *
     * @param $job
     * @param $data
     */
    public function fire($job,$data)
    {
        $this->notifynderSender->sendNow($data['info'],$data['category']);

        $job->delete();
    }
} 