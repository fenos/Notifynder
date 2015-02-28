<?php namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\Sender;
use Fenos\Notifynder\Contracts\StoreNotification;
use Fenos\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class SendSingle
 *
 * Send a single notification
 *
 * @package Fenos\Notifynder\Senders
 */
class SendOne implements Sender {

    /**
     * @var array
     */
    protected $infoNotification = [];

    /**
     * @param      $infoNotification
     */
    function __construct($infoNotification)
    {
        $this->infoNotification = $infoNotification;
    }

    /**
     * Send Single notification
     *
     * @param StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification)
    {
        $this->hasCategory();

        return $storeNotification->storeSingle($this->infoNotification);
    }

    /**
     * Check if the category of the notification has been
     * specified in the array of information
     *
     * @return bool
     * @throws \Fenos\Notifynder\Exceptions\CategoryNotFoundException
     */
    protected function hasCategory()
    {
        if (! array_key_exists('category_id', $this->infoNotification))
        {
            $error = "Category not found please provide one,
                     you can not store a notification without category id";

            throw new CategoryNotFoundException($error);
        }

        return true;
    }
}