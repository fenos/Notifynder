<?php
/**
 * Created by Fabrizio Fenoglio.
 */

namespace Fenos\Notifynder\Senders\Queue;

use Illuminate\Config\Repository;
use Illuminate\Queue\QueueManager;

/**
 * Class NotifynderQueue
 *
 * @package Fenos\Notifynder\Senders\Queue
 */
class NotifynderQueue {

    /**
     * @var QueueManager
     */
    protected $queueManager;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param Repository   $config
     * @param QueueManager $queueManager
     */
    function __construct(Repository $config, QueueManager $queueManager)
    {
        $this->config = $config;
        $this->queueManager = $queueManager;
    }

    /**
     * Push tje job to a queue
     *
     * @param array $info
     * @internal param $category
     * @return mixed
     */
    public function push(array $info)
    {
        return $this->queueManager->push('Fenos\Notifynder\Senders\Queue\QueueSender', $info);
    }

    /**
     * Check if the queue system is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->config->get('notifynder::config.queue');
    }
} 