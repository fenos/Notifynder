<?php

namespace Fenos\Notifynder\Senders;

use BadMethodCallException;
use Closure;
use Fenos\Notifynder\Contracts\DefaultSender;
use Fenos\Notifynder\Contracts\NotifynderSender;
use Fenos\Notifynder\Contracts\Sender;
use Fenos\Notifynder\Contracts\StoreNotification;
use Illuminate\Contracts\Container\Container;
use LogicException;

/**
 * Class SenderManager.
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
     * @var Container
     */
    protected $container;

    /**
     * @param SenderFactory     $senderFactory
     * @param StoreNotification $storeNotification
     * @param Container       $container
     */
    public function __construct(SenderFactory $senderFactory,
                         StoreNotification $storeNotification,
                         Container $container)
    {
        $this->senderFactory = $senderFactory;
        $this->storeNotification = $storeNotification;
        $this->container = $container;
    }

    /**
     * Send any notifications.
     *
     * @param  array $info
     * @param  null  $category
     * @return mixed
     */
    public function send($info, $category = null)
    {
        return $this->sendNow($info, $category);
    }

    /**
     * Send now whatever data passed.
     *
     * @param  array $info
     * @param        $category
     * @return mixed
     */
    public function sendNow($info, $category = null)
    {
        $sender = $this->senderFactory->getSender($info, $category);

        return $sender->send($this->storeNotification);
    }

    /**
     * Send one method to get fully working
     * older version.
     *
     * @param $info
     * @param $category
     * @return SendOne
     */
    public function sendOne($info, $category = null)
    {
        return $this->senderFactory->sendSingle($info, $category)
            ->send($this->storeNotification, $category);
    }

    /**
     * Send Multiple method to get fully working
     * older version.
     *
     * @param $info
     * @return SendMultiple
     */
    public function sendMultiple($info)
    {
        return $this->senderFactory->sendMultiple($info)
                    ->send($this->storeNotification);
    }

    /**
     * Send a group of notifications
     * at once.
     *
     * @param        $groupName
     * @param  array $info
     * @return mixed
     */
    public function sendGroup($groupName, $info = [])
    {
        return $this->senderFactory->sendGroup(
            $groupName,
            $info
        )->send($this->storeNotification);
    }

    /**
     * This method allow to Extend
     * notifynder with custom sender.
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
     * Call a custom method.
     *
     * @param $customMethod
     * @param $notification
     * @return mixed
     */
    public function customSender($customMethod, $notification)
    {
        if (array_key_exists($customMethod, $this->senders)) {

            // get the extended method
            $extendedSender = $this->senders[$customMethod];

            // If is a closure means that i'll return an instance
            // with the
            if ($extendedSender instanceof Closure) {

                // I invoke the closure expecting an Instance of a custom
                // Sender
                $invoker = call_user_func_array($extendedSender, [$notification, $this->container]);

                // If the invoker is a custom sender
                // then I invoke it passing the sender class
                if ($invoker instanceof Sender) {
                    return $invoker->send($this);
                }

                // If the dev is attempting to create a custom
                // way of storing notifications then
                // i'll pass the store notification contract
                if ($invoker instanceof DefaultSender) {
                    return $invoker->send($this->storeNotification);
                }
            }

            $error = 'The extension must be an instance of Closure';
            throw new LogicException($error);
        }

        $error = "The method $customMethod does not exists on the class ".get_class($this);
        throw new BadMethodCallException($error);
    }

    /**
     * When calling a not existing method
     * try to resolve with an extended.
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($arguments[0])) {
            return $this->customSender($name, $arguments[0]);
        }

        $error = 'No argument passed to the custom sender,
                 please provide notifications array';
        throw new BadMethodCallException($error);
    }
}
