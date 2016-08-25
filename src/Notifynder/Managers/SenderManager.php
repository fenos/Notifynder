<?php

namespace Fenos\Notifynder\Managers;

use BadFunctionCallException;
use BadMethodCallException;
use Closure;
use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class SenderManager
 * @package Fenos\Notifynder\Managers
 */
class SenderManager implements SenderManagerContract
{
    /**
     * @var array
     */
    protected $senders = [];

    /**
     * @param array $notifications
     * @return bool
     */
    public function send(array $notifications)
    {
        if (count($notifications) == 1) {
            return (bool) $this->sendSingle($notifications);
        }

        return (bool) $this->sendMultiple($notifications);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasSender($name)
    {
        return Arr::has($this->senders, $name);
    }

    /**
     * @param string $name
     * @return Closure
     */
    public function getSender($name)
    {
        return Arr::get($this->senders, $name);
    }

    /**
     * @param string $name
     * @param Closure $sender
     * @return bool
     */
    public function extend($name, Closure $sender)
    {
        if (Str::startsWith($name, 'send')) {
            $this->senders[$name] = $sender;

            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param array $notifications
     * @return bool
     * @throws BadFunctionCallException
     * @throws BadMethodCallException
     */
    public function sendWithCustomSender($name, array $notifications)
    {
        if ($this->hasSender($name)) {
            $sender = call_user_func_array($this->getSender($name), [$notifications]);
            if ($sender instanceof SenderContract) {
                return (bool) $sender->send($this);
            }
            throw new BadFunctionCallException("The sender [{$name}] hasn't returned an instance of ".SenderContract::class);
        }
        throw new BadMethodCallException("The sender [{$name}] isn't registered.");
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return bool
     * @throws BadMethodCallException
     */
    public function __call($name, array $arguments)
    {
        if (isset($arguments[0]) && is_array($arguments[0])) {
            return $this->sendWithCustomSender($name, $arguments[0]);
        }

        throw new BadMethodCallException('No argument passed to the custom sender, please provide notifications array');
    }
}
