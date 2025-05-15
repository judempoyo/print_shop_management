<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoSession extends Model
{
    const STATUS_PLANNED = 'planned';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PROCESSED = 'processed';

    protected $table = 'photo_sessions';
    protected $fillable = ['type', 'date', 'notes', 'status', 'customer_id', 'directory_path'];
    protected $dates = ['date'];


    public static function getStatuses()
    {
        return [
            self::STATUS_PLANNED => 'Planifiée',
            self::STATUS_COMPLETED => 'Terminée', 
            self::STATUS_PROCESSED => 'Traitée'
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class, 'session_id');
    }
}