<?php
/**
 * Created by Fabrizio Fenoglio.
 */
namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Exceptions\CategoryNotFoundException;

/**
 * Class SendSingle
 *
 * Delegate to send a single notification
 *
 * @package Fenos\Notifynder\Senders
 */
class SendOne implements Sender
{

    /**
     * @var array
     */
    protected $infoNotification = [];

    /**
     * @var \Fenos\Notifynder\Models\NotificationCategory
     */
    private $category;

    /**
     * @param      $infoNotification
     * @param null $category
     */
    public function __construct($infoNotification, $category = null)
    {
        $this->infoNotification = $infoNotification;
        $this->category = $category;
    }

    /**
     * Send Single notification
     *
     * @param  StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification)
    {
        $this->hasCategory();

        return $storeNotification->sendOne($this->infoNotification);
    }

    /**
     * Check if the category of the notification
     * has been specified
     *
     * @return bool
     * @throws \Fenos\Notifynder\Exceptions\CategoryNotFoundException
     */
    public function hasCategory()
    {
        if (is_null($this->category)) {
            $this->hasCategoryIdInInformation();

            return true;
        } else {
            $this->infoNotification['category_id'] = $this->category->id;

            return true;
        }
    }

    /**
     * Check if the category of the notification has been
     * specified in the array of information
     *
     * @throws \Fenos\Notifynder\Exceptions\CategoryNotFoundException
     */
    public function hasCategoryIdInInformation()
    {
        if (! array_key_exists('category_id', $this->infoNotification)) {
            $error = "Category not found please provide one,
                     you can not store a notification without category id";

            throw new CategoryNotFoundException($error);
        }
    }
}
