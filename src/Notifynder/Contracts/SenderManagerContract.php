<?php

namespace Fenos\Notifynder\Contracts;

use Closure;

interface SenderManagerContract
{
    public function send(array $notifications);

    public function hasSender($name);

    public function getSender($name);

    public function extend($name, Closure $sender);

    public function sendWithCustomSender($name, array $notifications);
}
