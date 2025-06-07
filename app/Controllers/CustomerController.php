<?php
namespace App\Controllers;

require_once __DIR__ . './../../vendor/autoload.php';

use App\Models\Customer;
use App\Core\ViewRenderer;
use Jump\JumpDataTable\DataTable;
use Jump\JumpDataTable\DataAction;
use Jump\JumpDataTable\DataColumn;
use Jump\JumpDataTable\Filter;

class CustomerController
{
    use ViewRenderer;
    protected $basePath;

    public function __construct()
    {
        $this->basePath = '/Projets/autres/hiernostine/public';
    }

    public function index()
    {
        
        $perPage = 10;
        $currentPage = $_GET['page'] ?? 1;
        $sort = $_GET['sort'] ?? 'id';
        $direction = $_GET['direction'] ?? 'asc';
        $search = $_GET['search'] ?? '';

        $allowedSorts = ['id', 'name', 'phone', 'email'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) $sort = 'id';
        if (!in_array($direction, $allowedDirections)) $direction = 'asc';

        $query = Customer::query();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $totalItems = $query->count();
        $offset = ($currentPage - 1) * $perPage;
        $customers = $query->orderBy($sort, $direction)
                          ->offset($offset)
                          ->limit($perPage)
                          ->get()
                          ->toArray();

        $table = DataTable::make()
            ->title('Liste des clients')
            ->modelName('customer')
            ->createUrl($this->basePath.'/customer/create')
            ->publicUrl($this->basePath)
            ->addColumn((new DataColumn('id', 'ID'))->sortable())
            ->addColumn((new DataColumn('name', 'Nom'))->searchable())
            ->addColumn((new DataColumn('email', 'Email'))->searchable())
            ->addColumn((new DataColumn('phone', 'Téléphone'))->searchable())
            ->addAction(DataAction::edit('Modifier', fn($item) => $this->basePath.'/customer/'.'edit/'.$item['id']))
            ->addAction(DataAction::delete('Supprimer', fn($item) => $this->basePath.'/customer/'.'delete/'.$item['id']))
                       ->data($customers)
            ->enableRowSelection(true)
            ->setBulkActions([
                DataAction::delete('Supprimer', fn($item) => "/delete/{$item}"),
            ])
            ->paginate($totalItems, $perPage, $currentPage, $this->basePath.'/customer', [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search
            ]);

        $this->render('app', 'customers/index', [
            'datatable' => $table->render(),
            'title' => 'Liste des clients'
        ]);
    }

    public function create()
    {
        $this->render('app', 'customers/create', [
            'title' => 'Ajouter un client'
        ]);
    }

    public function store()
    {
        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email'] ?? null),
            'phone' => trim($_POST['phone']),
            'address' => trim($_POST['address'] ?? null),
            'preferences' => !empty($_POST['preferences']) ? json_encode($_POST['preferences']) : null
        ];

     
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($data['phone'])) {
            $errors[] = "Le téléphone est obligatoire";
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo implode("\n", $errors);
            return;
        }

        Customer::create($data);

        Flash('message', 'Client créé avec succès', 'success');
        header('Location: ' . $this->basePath . '/customer');
    }

    public function edit($id)
    {
         if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de séance non fourni";
            return;
        }
        $customer = Customer::find($id);
  
        if (!$customer) {
            http_response_code(404);
            echo "Client non trouvé";
            return;
        }


        $this->render('app', 'customers/edit', [
            'customer' => $customer,
            'title' => 'Modifier le client'
        ]);
    }

    public function update($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            http_response_code(404);
            echo "Client non trouvé";
            return;
        }

        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email'] ?? null),
            'phone' => trim($_POST['phone']),
            'address' => trim($_POST['address'] ?? null),
        ];


        $errors = [];
        if (empty($data['name'])) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($data['phone'])) {
            $errors[] = "Le téléphone est obligatoire";
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo implode("\n", $errors);
            return;
        }

        $customer->update($data);

        Flash('message', 'Client modifié avec succès', 'success');

        header('Location: ' . $this->basePath . '/customer');
    }

    public function delete($id)
    {
          if (is_array($id)) {
            $id = $id['id'] ?? null; 
        }

        if (!$id) {
            http_response_code(400);
            echo "ID du client non fourni";
            return;
        }
        $customer = Customer::find($id);
        if (!$customer) {
            http_response_code(404);
            echo "Client non trouvé";
            return;
        }


        if ($customer->orders()->count() > 0) {
       
        Flash('message', 'Impossible de supprimer: ce client a des commandes associées', 'success');

            header('Location: ' . $this->basePath . '/customer');
            return;
        }

        $customer->delete();

        Flash('message', 'Client supprimé avec succès', 'success');

        header('Location: ' . $this->basePath . '/customer');
    }

    public function export()
    {
        $customers = Customer::all();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="clients_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'ID', 
            'Nom', 
            'Email', 
            'Téléphone', 
            'Adresse',
            'Date création'
        ]);


        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->phone,
                $customer->address,
                $customer->created_at
            ]);
        }

        fclose($output);
        exit;
    }
}