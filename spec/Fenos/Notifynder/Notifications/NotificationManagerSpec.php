<?php

namespace spec\Fenos\Notifynder\Notifications;

use Fenos\Notifynder\Contracts\NotificationDB;
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;
use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Models\NotifynderCollection;
use PhpSpec\ObjectBehavior;

class NotificationManagerSpec extends ObjectBehavior
{
    public function let(NotificationDB $notificationRepo)
    {
        $this->beConstructedWith($notificationRepo);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Notifications\NotificationManager');
    }

    /** @test */
    public function it_find_a_notification_by_id(NotificationDB $notificationRepo)
    {
        $notification_id = 1;
        $notification = new Notification();

        $notificationRepo->find($notification_id)->shouldBeCalled()
                         ->willReturn($notification);

        $this->find($notification_id)->shouldReturnAnInstanceOf(Notification::class);
    }

    /** @test */
    public function it_try_to_find_an_inexistent_notification(NotificationDB $notificationRepo)
    {
        $notification_id = 1;

        $notificationRepo->find($notification_id)->shouldBeCalled()
            ->willReturn(null);

        $this->shouldThrow(NotificationNotFoundException::class)->during('find', [$notification_id]);
    }

    /** @test */
    public function it_read_one_notification_by_id(NotificationDB $notificationRepo)
    {
        $notification_id = 1;
        $notification = new Notification();

        $notificationRepo->find($notification_id)->shouldBeCalled()
            ->willReturn($notification);

        $notificationRepo->readOne($notification)->shouldBeCalled()
                        ->willReturn($notification);

        $this->readOne($notification_id)->shouldReturnAnInstanceOf($notification);
    }

    /** @test */
    public function it_read_notifications_limit_to_a_given_number(NotificationDB $notificationRepo)
    {
        $to_id = 1;
        $numbers = 5;
        $order = 'asc';

        $notificationRepo->readLimit($to_id, null, $numbers, $order)->shouldBeCalled()
                ->willReturn($numbers);

        $this->readLimit($to_id, $numbers, $order)->shouldReturn($numbers);
    }

    /** @test */
    public function it_read_all_notification_of_the_given_entity(NotificationDB $notificationRepo)
    {
        $id = 1;
        $entity = null;
        $notificationDeleted = 10;

        $notificationRepo->readAll($id, $entity)->shouldBeCalled()
                    ->willReturn($notificationDeleted);

        $this->readAll($id)->shouldReturn($notificationDeleted);
    }

    /** @test */
    public function it_delete_a_notification_by_id(NotificationDB $notificationRepo)
    {
        $notification_id = 1;

        $notificationRepo->delete($notification_id)->shouldBeCalled()
                ->willReturn(1);

        $this->delete($notification_id)->shouldReturn(1);
    }

    /** @test */
    public function it_delete_notification_limiting_the_number_of_the_given_entity(NotificationDB $notificationRepo)
    {
        $entity_id = 1;
        $numberLimit = 5;
        $order = 'asc';

        $notificationRepo->deleteLimit($entity_id, null, $numberLimit, $order)->shouldBeCalled()
                    ->willReturn($numberLimit);

        $this->deleteLimit($entity_id, $numberLimit, $order)->shouldReturn($numberLimit);
    }

    /** @test */
    public function it_delete_all_notification_of_the_given_entity(NotificationDB $notificationRepo)
    {
        $entity_id = 1;
        $notificationsDeleted = 10;

        $notificationRepo->deleteAll($entity_id, null)->shouldBeCalled()
                ->willReturn($notificationsDeleted);

        $this->deleteAll($entity_id)->shouldReturn($notificationsDeleted);
    }

    /** @test */
    public function it_get_not_read_notification(NotificationDB $notificationRepo, NotifynderCollection $collection)
    {
        $entity_id = 1;
        $limit = 10;
        $paginate = null;

        $notificationRepo->getNotRead($entity_id, null, $limit, $paginate, 'desc', null)->shouldBeCalled()
                    ->willReturn($collection);

        $collection->parse()->shouldBeCalled()->willReturn([]);

        $this->getNotRead($entity_id, $limit, $paginate, 'desc')->shouldReturn([]);
    }

    /** @test */
    public function it_get_all_notifications_of_the_given_entity(NotificationDB $notificationRepo, NotifynderCollection $collection)
    {
        $entity_id = 1;
        $limit = 10;
        $paginate = null;

        $notificationRepo->getAll($entity_id, null, $limit, $paginate, 'desc', null)->shouldBeCalled()
            ->willReturn($collection);

        $collection->parse()->shouldBeCalled()->willReturn([]);

        $this->getAll($entity_id, $limit, $paginate)->shouldReturn([]);
    }

    /** @test */
    public function it_get_last_notification_of_the_current_entity(NotificationDB $notificationRepo)
    {
        $id = 1;

        $notificationRepo->getLastNotification($id, null, null)->shouldBeCalled()->willReturn(new Notification());

        $this->getLastNotification($id)->shouldReturnAnInstanceOf(Notification::class);
    }

    /** @test */
    public function it_get_last_notification_of_the_current_entity_filtering_by_category(NotificationDB $notificationRepo)
    {
        $id = 1;
        $category = 'notifynder.category';

        $notificationRepo->getLastNotificationByCategory($category, $id, null, null)->shouldBeCalled()->willReturn(new Notification());

        $this->getLastNotificationByCategory($category, $id)->shouldReturnAnInstanceOf(Notification::class);
    }

    /** @test */
    public function it_send_a_single_notification(NotificationDB $notificationRepo)
    {
        $notificationData = [];
        $notification = new Notification();

        $notificationRepo->storeSingle($notificationData)->shouldBeCalled()
                    ->willReturn($notification);

        $this->sendOne($notificationData)->shouldReturnAnInstanceOf(Notification::class);
    }

    /** @test */
    public function it_send_multiple_notification(NotificationDB $notificationRepo)
    {
        $notificationData = [];
        $notificationsSent = 5;

        $notificationRepo->storeMultiple($notificationData)->shouldBeCalled()
            ->willReturn($notificationsSent);

        $this->sendMultiple($notificationData)->shouldReturn($notificationsSent);
    }

    /** @test */
    public function it_count_notification_not_read(NotificationDB $notificationRepo)
    {
        $entity_id = 1;
        $notificationCount = 10;

        $notificationRepo->countNotRead($entity_id, null, null)->shouldBeCalled()
                    ->willReturn($notificationCount);

        $this->countNotRead($entity_id)->shouldReturn($notificationCount);
    }

    /** @test */
    public function it_delete_notification_by_categories(NotificationDB $notificationRepo)
    {
        $categoryName = 'notifynder.test';

        $notificationRepo->deleteByCategory($categoryName, false)->shouldBeCalled()
                ->willReturn(1);

        $this->deleteByCategory($categoryName)->shouldReturn(1);
    }
}
