<?php namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;
use Fenos\Notifynder\Models\Collections\NotifynderTranslationCollection;
use Fenos\Notifynder\Translator\NotifynderTranslator;
use Fenos\Notifynder\Parse\NotifynderParse;


/**
*
*/
class NotificationCategory extends Model
{
    protected $table = "notification_categories";
    protected $fillable = ['text','name'];

    /**
    * Custom Collection for the translations
    *
    */
    public function newCollection(array $models = array())
      {
      return new NotifynderTranslationCollection($models, new NotifynderTranslator );
      }

    public function notifications()
    {
        return $this->hasOne('Fenos\Notifynder\Models\Notification','category_id');
    }


}
