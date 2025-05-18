<?php
namespace App\Models;

use Illuminate\Support\Str;
use App\Services\RoleManager;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['email', 'password', 'role', 'name', 'phone'];


    public function getRoleNameAttribute(): string
    {
        return (new RoleManager())->getRoleName($this->role);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function can(string $permission): bool
    {
        $roleManager = new RoleManager();
        return $roleManager->hasPermission($this->id, $permission);
    }
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}