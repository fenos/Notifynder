<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;

/**
 * Class MultipleSender.
 */
class MultipleSender implements SenderContract
{
    /**
     * @var array
     */
    protected $notifications;

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $database;

    /**
     * MultipleSender constructor.
     *
     * @param array $notifications
     */
    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
        $this->database = app('db');
    }

    /**
     * Send all notifications.
     *
     * @param SenderManagerContract $sender
     * @return bool
     */
    public function send(SenderManagerContract $sender)
    {
        $model = app('notifynder.resolver.model')->getModel(Notification::class);
        $table = (new $model())->getTable();

        $this->database->beginTransaction();
        $stackId = $this->database
                ->table($table)
                ->max('stack_id') + 1;
        foreach ($this->notifications as $key => $notification) {
            $this->notifications[$key] = $this->notifications[$key]->toDbArray();
            $this->notifications[$key]['stack_id'] = $stackId;
        }
        $insert = $this->database
            ->table($table)
            ->insert($this->notifications);
        $this->database->commit();

        return (bool) $insert;
    }
}
