<?php

use Fenos\Notifynder\Models\Notification as ModelNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class NotifynderFacadeTest extends NotifynderTestCase
{
    public function testSendSingleNotification()
    {
        $sent = \Notifynder::category(1)
            ->from(1)
            ->to(2)
            ->send();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }
}
