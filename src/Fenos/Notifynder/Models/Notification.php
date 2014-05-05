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
	protected $fillable = ['from_id','from_type','to_id','to_type', 'category_id','url','extra', 'read'];

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

	public function from()
	{
		// check if on the configurations file there is the option 
		// polymorphic setted to true, if so Notifynder will work 
		// polymorphic.
		if ( Config::get('notifynder::polymorphic') === false )
		{
			return $this->belongsTo(Config::get('notifynder::config.model'),'from_id');
		}
		else {
			
			return $this->morphTo();
		}
	}

	public function to()
	{
		// check if on the configurations file there is the option 
		// polymorphic setted to true, if so Notifynder will work 
		// polymorphic.
		if ( Config::get('notifynder::notifynder.polymorphic') === false )
		{
			return $this->belongsTo(Config::get('notifynder::config.model'),'to_id');
		}
		else {
			
			return $this->morphTo();
		}
	}

	public function scopeGetNotifications($query,$to_id)
	{
		return $query->with('body','from')
						->where('to_id',$to_id)
						->where('read',0)
						->orderBy('created_at','DESC');
	}
}
