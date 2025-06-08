<?php
namespace App\Controllers;

use Exception;
use App\Models\User;
use App\Core\ViewRenderer;
use App\Services\RoleManager;
use Jump\JumpDataTable\Filter;
use App\Services\SessionManager;
use Jump\JumpDataTable\DataTable;
use Jump\JumpDataTable\DataAction;
use Jump\JumpDataTable\DataColumn;

class USerController
{
    use ViewRenderer;

    protected $session;
    protected $basePath;
    protected $roleManager;

    public function __construct()
    {
        $this->session = new SessionManager();
        $this->session->start();
        $this->basePath = '/Projets/autres/hiernostine/public';
        $this->roleManager = new RoleManager();
    }



    public function index()
    {
        $this->checkAdminAccess();

        $perPage = 10;
        $currentPage = $_GET['page'] ?? 1;
        $sort = $_GET['sort'] ?? 'id';
        $direction = $_GET['direction'] ?? 'asc';
        $search = $_GET['search'] ?? '';

        $allowedSorts = ['id', 'name', 'email', 'created_at'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts))
            $sort = 'id';
        if (!in_array($direction, $allowedDirections))
            $direction = 'asc';

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $totalItems = $query->count();
        $offset = ($currentPage - 1) * $perPage;

        $users = $query->orderBy($sort, $direction)
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function ($user) {
                $user->role_name = $this->roleManager->getRoleName($user->role);
                return $user;
            })
            ->toArray();

        $table = DataTable::make()
            ->title('Gestion des utilisateurs')
            ->modelName('user')
            ->createUrl($this->basePath . '/user/create')
            ->publicUrl($this->basePath)
            ->addColumn((new DataColumn('id', 'ID'))->sortable())
            ->addColumn((new DataColumn('name', 'Nom'))->searchable())
            ->addColumn((new DataColumn('email', 'Email'))->searchable())
            ->addColumn((new DataColumn('role_name', 'Rôle')))
            ->addColumn((new DataColumn('created_at', 'Date création'))->sortable())
            ->addAction(DataAction::edit('Modifier', fn($item) => $this->basePath . '/user/edit/' . $item['id']))
            ->addAction(DataAction::delete('Supprimer', fn($item) => $this->basePath . '/user/delete/' . $item['id']))
            ->data($users)
            ->enableRowSelection(true)
            ->setBulkActions([
                DataAction::delete('Supprimer', fn($item) => "/delete/{$item}"),
            ])
            ->paginate($totalItems, $perPage, $currentPage, $this->basePath . '/user', [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search
            ]);

        $this->render('app', 'users/index', [
            'datatable' => $table->render(),
            'title' => 'Gestion des utilisateurs',
            'roles' => $this->roleManager->getAllRoles()
        ]);
    }

    public function create()
    {
        $this->checkAdminAccess();

        $this->render('app', 'users/create', [
            'title' => 'Créer un utilisateur',
            'roles' => $this->roleManager->getAllRoles(),
            'sessionManager' => $this->session,

        ]);
    }


    public function store()
    {
        $this->checkAdminAccess();

        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'password_confirmation' => $_POST['password_confirmation'],
            'role' => $_POST['role'] ?? 'user'
        ];

        $errors = $this->validateUserData($data, true);

        if (!empty($errors)) {
            $this->session->set('errors', $errors);
            $this->session->set('old', $data);
            header('Location: ' . $this->basePath . '/user/create');
            exit();
        }

        try {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            $user->role = $data['role'];
            $user->save();

            $this->session->set('message', 'Utilisateur créé avec succès.');
            header('Location: ' . $this->basePath . '/user');
            exit();
        } catch (Exception $e) {
            $this->session->set('error', 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/user/create');
            exit();
        }
    }


    public function edit($userId)
    {
        $this->checkAdminAccess();
        if (is_array($userId)) {
            $userId = $userId['id'] ?? null;
        }

        if (!$userId) {
            http_response_code(400);
            echo "ID de séance non fourni";
            return;
        }
        $user = User::find($userId);
        if (!$user) {
            $this->session->set('error', 'Utilisateur introuvable.');
            header('Location: ' . $this->basePath . '/user');
            exit();
        }

        $this->render('app', 'users/edit', [
            'title' => 'Modifier l\'utilisateur',
            'user' => $user,
            'roles' => $this->roleManager->getAllRoles(),
            'sessionManager' => $this->session,
            'errors' => $this->session->get('errors'),
            'old' => $this->session->get('old')
        ]);
        $this->session->remove(['errors', 'old']);
    }


    public function update($userId)
    {
        $this->checkAdminAccess();

        $user = User::find($userId);
        if (!$user) {
            $this->session->set('error', 'Utilisateur introuvable.');
            header('Location: ' . $this->basePath . '/user');
            exit();
        }

        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'role' => $_POST['role'] ?? $user->role,
            'password' => $_POST['password'] ?? null,
            'password_confirmation' => $_POST['password_confirmation'] ?? null
        ];

        $errors = $this->validateUserData($data, !empty($data['password']));

        if (!empty($errors)) {
            $this->session->set('errors', $errors);
            $this->session->set('old', $data);
            header('Location: ' . $this->basePath . '/user/edit/' . $userId);
            exit();
        }

        try {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $data['role'];

            if (!empty($data['password'])) {
                $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $user->save();

            $this->session->set('message', 'Utilisateur mis à jour avec succès.');
            header('Location: ' . $this->basePath . '/user');
            exit();
        } catch (Exception $e) {
            $this->session->set('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/user/edit/' . $userId);
            exit();
        }
    }


    public function delete($userId)
    {
        $this->checkAdminAccess();

        try {
            $user = User::find($userId);
            if (!$user) {
                throw new Exception('Utilisateur introuvable');
            }

            if ($user->id === 1) {
                throw new Exception('Impossible de supprimer l\'administrateur principal');
            }

            $user->delete();
            $this->session->set('message', 'Utilisateur supprimé avec succès.');
        } catch (Exception $e) {
            $this->session->set('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }

        header('Location: ' . $this->basePath . '/user');
        exit();
    }
    private function validateUserData(array $data, bool $checkPassword = false): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Le nom est obligatoire.';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est obligatoire.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email est invalide.';
        } elseif (User::where('email', $data['email'])->where('id', '!=', $data['id'] ?? null)->exists()) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        if ($checkPassword) {
            if (empty($data['password'])) {
                $errors['password'] = 'Le mot de passe est obligatoire.';
            } elseif (strlen($data['password']) < 8) {
                $errors['password'] = 'Le mot de passe doit faire au moins 8 caractères.';
            } elseif ($data['password'] !== $data['password_confirmation']) {
                $errors['password'] = 'Les mots de passe ne correspondent pas.';
            }
        }

        if (!$this->roleManager->roleExists($data['role'])) {
            $errors['role'] = 'Rôle invalide.';
        }

        return $errors;
    }


    private function checkAdminAccess()
    {
        if (!$this->session->has('user') || !$this->roleManager->isAdmin($this->session->get('user'))) {
            $this->session->set('message', 'Accès refusé. Droits insuffisants.');
            header('Location: ' . $this->basePath . '/');
            exit();
        }
    }
}