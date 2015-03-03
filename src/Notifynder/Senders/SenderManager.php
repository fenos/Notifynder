<?php namespace Fenos\Notifynder\Senders;

use BadMethodCallException;
use Closure;
use Fenos\Notifynder\Contracts\NotifynderSender;
use Fenos\Notifynder\Contracts\StoreNotification;
use Illuminate\Contracts\Foundation\Application;
use LogicException;

/**
 * Class SenderManager
 *
 * @package Fenos\Notifynder\Senders
 */
class SenderManager implements NotifynderSender
{

    /**
     * @var SenderFactory
     */
    protected $senderFactory;

    /**
     * @var StoreNotification
     */
    protected $storeNotification;

    /**
     * @var array
     */
    protected $senders = [];

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param SenderFactory     $senderFactory
     * @param StoreNotification $storeNotification
     * @param Application       $app
     */
    public function __construct(SenderFactory $senderFactory,
                         StoreNotification $storeNotification,
                         Application $app)
    {
        $this->senderFactory = $senderFactory;
        $this->storeNotification = $storeNotification;
        $this->app = $app;
    }

    /**
     * Send any notifications
     *
     * @param  array $info
     * @param  null  $category
     * @return mixed
     */
    public function send(array $info, $category = null)
    {
        return $this->sendNow($info, $category);
    }

    /**
     * Send now whatever data passed
     *
     * @param  array $info
     * @param        $category
     * @return mixed
     */
    public function sendNow(array $info, $category = null)
    {
        $sender = $this->senderFactory->getSender($info, $category);

        return $sender->send($this->storeNotification);
    }

    /**
     * Send one method to get fully working
     * older version
     *
     * @param $info
     * @param $category
     * @return SendOne
     */
    public function sendOne(array $info, $category)
    {
        return $this->senderFactory->sendSingle($info, $category)
            ->send($this->storeNotification, $category);
    }

    /**
     * Send Multiple method to get fully working
     * older version
     *
     * @param $info
     * @return SendMultiple
     */
    public function sendMultiple(array $info)
    {
        return $this->senderFactory->sendMultiple($info)
                    ->send($this->storeNotification);
    }

    /**
     * Send a group of notifications
     * at once
     *
     * @param        $group_name
     * @param  array $info
     * @return mixed
     */
    public function sendGroup($group_name, array $info = [])
    {
        return $this->senderFactory->sendGroup(
            $group_name,
            $info
        )->send($this->storeNotification);
    }

    /**
     * This method allow to Extend
     * notifynder with custom sender
     *
     * @param           $name
     * @param  callable $extendSender
     * @return $this
     */
    public function extend($name, $extendSender)
    {
        $this->senders[$name] = $extendSender;

        return $this;
    }

    /**
     * Call the extended method
     * when calling a undefined method
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Check if the method called exists on the extended methods
        if (array_key_exists($name, $this->senders)) {
            // get the extended method
            $extendedSender = $this->senders[$name];

            if (is_string($extendedSender)) {
                //                dd($arguments);
                return $this->app->make($extendedSender, $arguments)
                            ->send($this->storeNotification);
            }

            // If is a closure means that i'll return an instance
            // with the
            if ($extendedSender instanceof Closure) {
                array_unshift($arguments, [$this->app]);

                $invoker = call_user_func_array($extendedSender, $arguments);

                return $invoker->send($this->storeNotification);
            }

            $error = "The custom method must implement Sender Contract";
            throw new LogicException($error);
        }

        $error = "The method $name does not exists on the class ".get_class($this);
        throw new BadMethodCallException($error);
    }
}
