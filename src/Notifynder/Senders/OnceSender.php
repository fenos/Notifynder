<?php
namespace Fenos\Notifynder\Senders;

use BadMethodCallException;
use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class OnceSender implements SenderContract
{
    protected $notifications;

    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }

    public function send(SenderManagerContract $sender)
    {
        $model = notifynder_config()->getNotificationModel();

        $success = true;
        foreach($this->notifications as $notification) {
            $query = $model::query();
            if (!($query instanceof EloquentBuilder)) {
                throw new BadMethodCallException("The query method hasn't return an instance of the eloquent query builder.");
            }
            $query
                ->where('from_id', $notification->from_id)
                ->where('from_type', $notification->from_type)
                ->where('to_id', $notification->to_id)
                ->where('to_type', $notification->to_type)
                ->where('category_id', $notification->category_id);
            if (isset($notification->extra) && !empty($notification->extra)) {
                $extra = $notification->extra;
                if(is_array($extra)) {
                    $extra = json_encode($extra);
                }
                $query->where('extra', $extra);
            }
            if (!$query->exists()) {
                $success = $sender->send([$notification]) ? $success : false;
            } else {
                $notification = $query->first();
                $notification->touch();
                $notification->unread();
            }
        }
        return $success;
    }
}