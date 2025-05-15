<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'photos';
    protected $fillable = ['filename', 'path', 'session_id', 'customer_id'];

    public function session()
    {
        return $this->belongsTo(PhotoSession::class, 'session_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}