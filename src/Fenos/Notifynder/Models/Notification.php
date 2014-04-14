<?php namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;
use Config;
/**
* 
*/
class Notification extends Model
{
	protected $table = "notifications";
	protected $fillable = ['from_id','to_id','category_id','url','read'];

	public function body()
	{
		return $this->belongsTo('Fenos\Notifynder\Models\NotificationCategory');
	}

	public function user()
	{
		return $this->belongsTo(Config::get('notifynder::model'),'from_id');
	}
}
