<?php

namespace Fenos\Notifynder\Contracts;

interface SenderContract
{
    public function __construct(array $notifications);

    public function send(SenderManagerContract $sender);
}
