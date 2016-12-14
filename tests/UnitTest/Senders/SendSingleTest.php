<?php

use Fenos\Notifynder\Senders\SendOne;
use Mockery as m;

class SendSingleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SendOne
     */
    protected $sendSingle;

    /**
     * @var array
     */
    protected $dataNotification;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        $this->sendSingle = new SendOne(
            $this->dataNotification = NotificationDataBuilder::singleNotificationData()
        );
    }

    /**
     * TearDown.
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_send_single_notification_stroing_it_in_the_db()
    {
        $storeNotification = m::mock('Fenos\Notifynder\Senders\StoreNotification');

        $storeNotification->shouldReceive('sendOne')
            ->once()
            ->with($this->dataNotification)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->sendSingle->send($storeNotification);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_check_if_the_category_id_is_present_on_the_information()
    {
        $mockSenderSingle = m::mock('Fenos\Notifynder\Senders\SendOne[hasCategoryIdInInformation]', [$this->dataNotification]);

        $mockSenderSingle->shouldReceive('hasCategoryIdInInformation')
             ->once()
             ->with()
             ->andReturn(true);

        $result = $mockSenderSingle->hasCategory();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_check_if_the_category_id_is_set_in_the_property()
    {
        $categoryModel = m::mock('Fenos\Notifynder\Models\NotificationCategory');
        $senderSingle = new SendOne(
            $this->dataNotification,
            $categoryModel
        );

        $categoryModel->shouldReceive('setAttribute')
            ->once()
            ->andReturn($categoryModel);

        $categoryModel->shouldReceive('getAttribute')
            ->once()
            ->andReturn($categoryModel);

        $categoryModel->id = 2;

        $result = $senderSingle->hasCategory();

        $this->assertTrue($result);
    }

    /** @test */
    public function it_check_if_the_category_id_has_been_inserted()
    {
        $result = $this->sendSingle->hasCategoryIdInInformation();

        $this->assertNull($result);
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\CategoryNotFoundException
     * */
    public function it_check_if_the_category_id_has_been_inserted_but_it_is_not()
    {
        $classSender = new SendOne([], null);

        $classSender->hasCategoryIdInInformation();
    }
}
