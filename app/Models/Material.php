<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    protected $table = 'materials';
    protected $fillable = [
        'name',
        'type',
        'stock_quantity',
        'unit',
        'min_stock_level',
        'cost_per_unit'
    ];
    

    protected $casts = [
        'stock_quantity' => 'int',

    ];
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_materials')
                    ->withPivot('quantity_used')
                     ->withTimestamps();
    }
}