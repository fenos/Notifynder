<?php
/**
 * Created by Fabrizio Fenoglio.
 */
namespace Fenos\Notifynder\Senders;


/**
 * Class SendersDB
 *
 * @package Fenos\Notifynder\Senders
 */
interface StoreNotification {

    /**
     * Save a single notification sent
     *
     * @param array $info
     * @return static
     */
    public function sendOne(array $info);

    /**
     * Save multiple notifications sent
     * at once
     *
     * @param array $info
     * @return mixed
     */
    public function sendMultiple(array $info);
}