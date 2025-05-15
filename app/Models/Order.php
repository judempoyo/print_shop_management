<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'reference',
        'delivery_date',
        'priority',
        'status',
        'notes'
    ];
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function productionSteps(): HasMany
    {
        return $this->hasMany(ProductionStep::class);
    }
    
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
    
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'order_materials')
                    ->withPivot('quantity_used')
                    ->withTimestamps(false);
                    
    }
}