<?php

namespace Fenos\Notifynder\Managers;

use BadFunctionCallException;
use BadMethodCallException;
use Closure;
use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SenderManager implements SenderManagerContract
{
    protected $senders = [];

    public function send(array $notifications)
    {
        if (count($notifications) == 1) {
            return $this->sendSingle($notifications);
        }

        return $this->sendMultiple($notifications);
    }

    public function hasSender($name)
    {
        return Arr::has($this->senders, $name);
    }

    public function getSender($name)
    {
        return Arr::get($this->senders, $name);
    }

    public function extend($name, Closure $sender)
    {
        if (Str::startsWith($name, 'send')) {
            $this->senders[$name] = $sender;

            return true;
        }

        return false;
    }

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

    public function __call($name, $arguments)
    {
        if (isset($arguments[0]) && is_array($arguments[0])) {
            return $this->sendWithCustomSender($name, $arguments[0]);
        }

        throw new BadMethodCallException('No argument passed to the custom sender, please provide notifications array');
    }
}
