<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    protected $table = 'files';
    protected $fillable = [
        'order_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size'
    ];
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}