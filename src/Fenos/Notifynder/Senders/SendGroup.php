<?php namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Groups\NotifynderGroup;
use Fenos\Notifynder\Notifynder;

/**
 * Class SendGroup
 *
 * @package Fenos\Notifynder\Senders
 */
class SendGroup implements Sender {

    /**
     * @var Notifynder
     */
    protected $notifynder;

    /**
     * @var NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var string
     */
    protected $nameGroup;

    /**
     * @var array
     */
    protected $info;

    /**
     * @param Notifynder        $notifynder
     * @param NotifynderGroup   $notifynderGroup
     * @param string            $nameGroup
     * @param array | \Closure  $info
     */
    function __construct(Notifynder $notifynder,
                         NotifynderGroup $notifynderGroup,
                         $nameGroup,
                         $info )
    {
        $this->info = $info;
        $this->nameGroup = $nameGroup;
        $this->notifynder = $notifynder;
        $this->notifynderGroup = $notifynderGroup;
    }

    /**
     * Send group notifications
     *
     * @param StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification)
    {
        $group = $this->notifynderGroup->findGroupByName($this->nameGroup);

        $categoriesAssociated = $group->categories;

        // Send a notification for each category
        foreach($categoriesAssociated as $category)
        {
            $this->sendLoop($category);
        }

        return $group;
    }

    /**
     * @param $category
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function sendLoop($category)
    {
        if (is_array($this->info))
        {
            return $this->notifynder->category($category->name)->send($this->info);

        } elseif ($this->info instanceof \Closure)
        {
            $closure = $this->info;
            return $closure($this->notifynder,$category->name);
        }

        $error = "The information given must be an array or instance of Closure";
        throw new \InvalidArgumentException($error);
    }
}