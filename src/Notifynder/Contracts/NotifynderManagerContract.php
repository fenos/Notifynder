<?php

namespace Fenos\Notifynder\Contracts;

use Closure;

/**
 * Interface NotifynderManagerContract.
 */
interface NotifynderManagerContract
{
    /**
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return $this
     */
    public function category($category);

    /**
     * @param array|\Traversable $data
     * @param Closure $callback
     * @return $this
     */
    public function loop($data, Closure $callback);

    /**
     * @return bool
     */
    public function send();

    /**
     * @param bool $force
     * @return \Fenos\Notifynder\Builder\Builder
     */
    public function builder($force = false);

    /**
     * @return SenderManagerContract
     */
    public function sender();

    /**
     * @param string $name
     * @param Closure $sender
     * @return bool
     */
    public function extend($name, Closure $sender);
}
