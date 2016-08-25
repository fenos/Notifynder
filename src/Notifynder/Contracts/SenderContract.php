<?php

namespace Fenos\Notifynder\Contracts;

/**
 * Interface SenderContract.
 */
interface SenderContract
{
    /**
     * SenderContract constructor.
     *
     * @param array $notifications
     */
    public function __construct(array $notifications);

    /**
     * @param SenderManagerContract $sender
     * @return bool
     */
    public function send(SenderManagerContract $sender);
}
