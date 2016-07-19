<?php

namespace Fenos\Notifynder\Managers;

use BadMethodCallException;
use Closure;
use Fenos\Notifynder\Builder\Builder;
use Fenos\Notifynder\Contracts\NotifynderManagerContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Illuminate\Support\Str;

class NotifynderManager implements NotifynderManagerContract
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $sender;

    public function __construct(SenderManagerContract $sender)
    {
        $this->sender = $sender;
    }

    public function category($category)
    {
        $this->builder(true);
        $this->builder->category($category);

        return $this;
    }

    public function loop($data, Closure $callback)
    {
        $this->builder(true);
        $this->builder->loop($data, $callback);

        return $this;
    }

    public function builder($new = false)
    {
        if (is_null($this->builder) || $new) {
            $this->builder = new Builder();
        }

        return $this->builder;
    }

    public function send()
    {
        $sent = $this->sender->send($this->builder->getNotifications());
        $this->reset();

        return $sent;
    }

    public function sender()
    {
        return $this->sender;
    }

    protected function reset()
    {
        $this->builder = null;
    }

    public function extend($name, Closure $sender)
    {
        return $this->sender->extend($name, $sender);
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'send')) {
            $sent = $this->sender->sendWithCustomSender($name, $this->builder->getNotifications());
            $this->reset();

            return $sent;
        }

        if ($this->builder instanceof Builder && method_exists($this->builder, $name)) {
            $result = call_user_func_array([$this->builder, $name], $arguments);
            if (Str::startsWith($name, 'get')) {
                return $result;
            }

            return $this;
        }

        $error = "The method [$name] doesn't exist in the class " . self::class;
        throw new BadMethodCallException($error);
    }
}
