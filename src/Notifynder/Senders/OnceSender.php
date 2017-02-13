<?php

namespace Fenos\Notifynder\Senders;

use BadMethodCallException;
use Fenos\Notifynder\Builder\Notification;
use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Fenos\Notifynder\Models\Notification as NotificationModel;

/**
 * Class OnceSender.
 */
class OnceSender implements SenderContract
{
    /**
     * @var array
     */
    protected $notifications;

    /**
     * OnceSender constructor.
     *
     * @param array $notifications
     */
    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Send the notification once.
     *
     * @param SenderManagerContract $sender
     * @return bool
     */
    public function send(SenderManagerContract $sender)
    {
        $success = true;
        foreach ($this->notifications as $notification) {
            $query = $this->getQuery($notification);
            if (! $query->exists()) {
                $success = $sender->send([$notification]) ? $success : false;
                continue;
            }
            $success = $query->firstOrFail()->resend() ? $success : false;
        }

        return $success;
    }

    /**
     * Get the base query.
     *
     * @param Notification $notification
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQuery(Notification $notification)
    {
        $query = $this->getQueryInstance();
        $query
            ->where('from_id', $notification->from_id)
            ->where('from_type', $notification->from_type)
            ->where('to_id', $notification->to_id)
            ->where('to_type', $notification->to_type)
            ->where('category_id', $notification->category_id);
        $extra = $notification->extra;
        if (! is_null($extra) && ! empty($extra)) {
            if (is_array($extra)) {
                $extra = json_encode($extra);
            }
            $query->where('extra', $extra);
        }

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQueryInstance()
    {
        $model = app('notifynder.resolver.model')->getModel(NotificationModel::class);
        $query = $model::query();
        if (! ($query instanceof EloquentBuilder)) {
            throw new BadMethodCallException("The query method hasn't return an instance of [".EloquentBuilder::class.'].');
        }

        return $query;
    }
}
