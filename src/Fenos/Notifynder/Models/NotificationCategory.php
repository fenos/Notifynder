<?php namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class NotificationCategory extends Model
{
	protected $table = "notification_categories";
	protected $fillable = ['text','name'];
	
	public function notifications()
	{
		return $this->hasOne('Fenos\Notifynder\Models\Notification','type_id');
	}
}