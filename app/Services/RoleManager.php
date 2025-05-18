<?php
namespace App\Services;

use App\Models\User;

class RoleManager
{
    const ROLES = [
        'admin' => 'Administrateur',
        'manager' => 'Responsable production',
        'operator' => 'Opérateur',
        'client' => 'Client',
        'user' => 'Utilisateur standard'
    ];

    public function getAllRoles(): array
    {
        return self::ROLES;
    }

    public function getRoleName(string $roleKey): string
    {
        return self::ROLES[$roleKey] ?? $roleKey;
    }

    public function roleExists(string $role): bool
    {
        return array_key_exists($role, self::ROLES);
    }

    public function isAdmin(int $userId): bool
    {
        $user = User::find($userId);
        return $user && $user->role === 'admin';
    }

    public function hasPermission(int $userId, string $requiredRole): bool
    {
        $user = User::find($userId);
        if (!$user) return false;

        $roleHierarchy = [
            'admin' => 4,
            'manager' => 3,
            'operator' => 2,
            'client' => 1,
            'user' => 0
        ];

        $userLevel = $roleHierarchy[$user->role] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;

        return $userLevel >= $requiredLevel;
    }
}