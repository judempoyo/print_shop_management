<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  protected $table = 'customers'; 
  protected $fillable = ['name', 'phone','preferences'];

  public function photoSessions()
  {
      return $this->hasMany(PhotoSession::class);
  }
  public function photos()
  {
      return $this->hasMany(Photo::class, 'customer_id');
  }
}
