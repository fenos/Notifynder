<?php
/**
 * Created by Fabrizio Fenoglio.
 */
namespace Fenos\Notifynder\Senders;


/**
 * Class SendMultiple
 *
 * @package Fenos\Notifynder\Senders
 */
interface Sender {

    /**
     * Send multiple notifications
     *
     * @param StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification);
}