<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionStep extends Model
{
    protected $table = 'production_steps';
    protected $fillable = [
        'order_id',
        'step',
        'status',
        'assigned_to',
        'start_time',
        'end_time',
        'comments'
    ];
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}