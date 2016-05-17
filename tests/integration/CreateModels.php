<?php

use Fenos\Notifynder\Models\Notification;
use Laracasts\TestDummy\Factory;

trait CreateModels
{
    /**
     * Create Category.
     *
     * @param array $data
     * @return mixed
     */
    protected function createCategory(array $data = [])
    {
        return Factory::create('Fenos\Notifynder\Models\NotificationCategory', $data);
    }

    /**
     * Create Group.
     *
     * @param array $data
     * @return mixed
     */
    protected function createGroup(array $data = [])
    {
        return Factory::create('Fenos\Notifynder\Models\NotificationGroup', $data);
    }

    /**
     * Shortcut to create a new notification.
     *
     * @param array $data
     * @return mixed
     */
    protected function createNotification(array $data = [])
    {
        return Factory::create(Notification::class, $data);
    }

    /**
     * Shortcut Multi notifications.
     *
     * @param array $data
     * @return mixed
     */
    protected function createMultipleNotifications(array $data = [])
    {
        $to_entity = [
            'to_id'   => $this->to['id'],
            'to_type' => $this->to['type'],
            'read'    => 0,
        ];

        return Factory::times($this->multiNotificationsNumber)
            ->create(Notification::class, array_merge($to_entity, $data));
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function createUser(array $data = [])
    {
        return Factory::create('Fenos\Tests\Models\User', $data);
    }
}
