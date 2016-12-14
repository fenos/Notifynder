<?php
/**
 * Created by Fabrizio Fenoglio.
 */

namespace Fenos\Notifynder\Senders;

/**
 * Class SendMultiple.
 */
interface Sender
{
    /**
     * Send multiple notifications.
     *
     * @param StoreNotification $storeNotification
     *
     * @return mixed
     */
    public function send(StoreNotification $storeNotification);
}
