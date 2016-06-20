<?php
namespace Fenos\Notifynder\Senders;


use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;

class MultipleSender implements SenderContract
{
    protected $notifications;

    protected $database;

    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
        $this->database = app('db');
    }

    public function send(SenderManagerContract $sender)
    {
        $model = notifynder_config()->getNotificationModel();
        $table = (new $model())->getTable();

        $this->database->beginTransaction();
        $stackId = $this->database
                ->table($table)
                ->max('stack_id') + 1;
        foreach ($this->notifications as $key => $notification) {
            $this->notifications[$key] = $this->notifications[$key]->toArray();
            $this->notifications[$key]['stack_id'] = $stackId;
        }
        $insert = $this->database
            ->table($table)
            ->insert($this->notifications);
        $this->database->commit();
        return $insert;
    }
}