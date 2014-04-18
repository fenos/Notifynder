<?php namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;
use Fenos\Notifynder\Models\Collections\NotifynderTranslationCollection;
use Fenos\Notifynder\Translator\NotifynderTranslator;
use Fenos\Notifynder\Parse\NotifynderParse;
use Config;

/**
* 
*/
class Notification extends Model
{
	protected $table = "notifications";
	protected $fillable = ['from_id','to_id','category_id','url','extra', 'read'];

	/**
	* Custom Collection for the translations
	*
	*/
	public function newCollection(array $models = array())
  	{
      return new NotifynderTranslationCollection($models, new NotifynderTranslator);
  	}

	public function body()
	{
		return $this->belongsTo('Fenos\Notifynder\Models\NotificationCategory','category_id');
	}

	public function user()
	{
		return $this->belongsTo(Config::get('notifynder::model'),'from_id');
	}
}
