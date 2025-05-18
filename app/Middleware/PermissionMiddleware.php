<?php
namespace App\Middleware;

use App\Services\SessionManager;
use App\Services\RoleManager;

class PermissionMiddleware
{
    protected $session;
    protected $basePath;
    protected $roleManager;

    public function __construct(SessionManager $session, string $basePath)
    {
        $this->session = $session;
        $this->basePath = $basePath;
        $this->roleManager = new RoleManager();
    }

    public function handle(string $requiredRole)
    {
        if (!$this->session->has('user')) {
            $this->session->set('error', 'Vous devez être connecté pour accéder à cette page.');
            header('Location: ' . $this->basePath . '/login');
            exit();
        }

        if (!$this->roleManager->hasPermission($this->session->get('user'), $requiredRole)) {
            $this->session->set('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            header('Location: ' . $this->basePath . '/');
            exit();
        }
    }
}