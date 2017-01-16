<?php

namespace Fenos\Notifynder\Contracts;

use Closure;

/**
 * Interface SenderManagerContract.
 */
interface SenderManagerContract
{
    /**
     * @param array $notifications
     * @return bool
     */
    public function send(array $notifications);

    /**
     * @param string $name
     * @return bool
     */
    public function hasSender($name);

    /**
     * @param string $name
     * @return Closure|null
     */
    public function getSender($name);

    /**
     * @param string $name
     * @param Closure $sender
     * @return bool
     */
    public function extend($name, Closure $sender);

    /**
     * @param string $class
     * @param callable $callback
     * @return bool
     */
    public function setCallback($class, callable $callback);

    /**
     * @param string $class
     * @return callable|null
     */
    public function getCallback($class);

    /**
     * @param string $name
     * @param array $notifications
     * @return bool
     */
    public function sendWithCustomSender($name, array $notifications);
}
