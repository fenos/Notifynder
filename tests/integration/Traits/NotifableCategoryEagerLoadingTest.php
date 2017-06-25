<?php

use Fenos\Notifynder\Builder\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotifableCategoryEagerLoadingTest extends NotifynderTestCase
{
    private $initialLogStatus;
    
    public function setUp()
    {
        parent::setUp();
        if (!app('db')->connection()->logging()) {
            app('db')->connection()->enableQueryLog();
            $this->initialLogStatus = false;
        } else {
            $this->initialLogStatus = true;
        }
    }

    public function tearDown()
    {
        if ($this->initialLogStatus == false) {
            app('db')->connection()->disableQueryLog();
        }
        parent::tearDown();
    }

    public function testGetNotificationsWithEagerLoadedCategories()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['text' => 'A Test Notification']);
        $this->sendNotificationsTo($user, 25, $category);

        $startCount = count(app('db')->getQueryLog());
        $notifications = $user->getNotificationRelation()
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->get();
        foreach ($notifications as $notification) {
            $notification->text;
        }
        $endCount = count(app('db')->getQueryLog());

        $this->assertSame(2, $endCount - $startCount);
    }

    public function testThrowsModelNotFoundExceptionIfCategoryIsNull()
    {
        $category = $this->createCategory();
        $from = $this->createUser();
        $to = $this->createUser();
        $notification = new Notification();
        $notification->set('category_id', null);
        $notification->set('from_id', $from->getKey());
        $notification->set('to_id', $to->getKey());

        $this->expectException(ModelNotFoundException::class);
        $notification->getText();
    }
}
