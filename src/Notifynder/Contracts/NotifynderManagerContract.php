<?php

namespace Fenos\Notifynder\Contracts;

use Closure;

interface NotifynderManagerContract
{
    public function category($category);

    public function loop($data, Closure $callback);

    public function send();

    public function builder();
}
