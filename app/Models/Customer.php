<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'address',
        'preferences'
    ];
    
    protected $casts = [
        'preferences' => 'array'
    ];
    
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}