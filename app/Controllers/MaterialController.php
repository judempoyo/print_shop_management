<?php
namespace App\Controllers;

require_once __DIR__ . './../../vendor/autoload.php';

use App\Models\Material;
use App\Core\ViewRenderer;
use Jump\JumpDataTable\DataTable;
use Jump\JumpDataTable\DataAction;
use Jump\JumpDataTable\DataColumn;
use Jump\JumpDataTable\Filter;

class MaterialController
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
        $direction = $_GET['direction'] ?? 'desc';
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? null;

        $allowedSorts = ['id', 'name', 'type', 'stock_quantity'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) $sort = 'id';
        if (!in_array($direction, $allowedDirections)) $direction = 'desc';

        $query = Material::query();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        $totalItems = $query->count();
        $offset = ($currentPage - 1) * $perPage;
        $materials = $query->orderBy($sort, $direction)
                          ->offset($offset)
                          ->limit($perPage)
                          ->get()
                          ->toArray();

        $table = DataTable::make()
            ->title('Gestion des matériaux')
            ->modelName('material')
            ->createUrl($this->basePath.'/material/create')
            ->publicUrl($this->basePath)
            ->addColumn((new DataColumn('id', 'ID'))->sortable())
            ->addColumn((new DataColumn('name', 'Nom'))->searchable())
            ->addColumn((new DataColumn('type', 'Type'))->sortable())
            ->addColumn((new DataColumn('stock_quantity', 'Stock'))->sortable())
            ->addColumn((new DataColumn('unit', 'Unité')))
            ->addAction(DataAction::edit('Modifier', fn($item) => $this->basePath.'/material/'.'edit/'.$item['id']))
            ->addAction(DataAction::delete('Supprimer', fn($item) => $this->basePath.'/material/'.'delete/'.$item['id']))
            ->data($materials)
            ->addFilter(new Filter('type', 'Type', [
                'paper' => 'Papier',
                'ink' => 'Encre',
                'plate' => 'Plaque',
                'chemical' => 'Chimique',
                'other' => 'Autre'
            ]))
            ->enableRowSelection(true)
            ->setBulkActions([
                DataAction::delete('Supprimer', fn($item) => "/delete/{$item}"),
            ])
            ->paginate($totalItems, $perPage, $currentPage, $this->basePath.'/material', [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
                'type' => $type
            ]);

        $this->render('app', 'materials/index', [
            'datatable' => $table->render(),
            'title' => 'Gestion des matériaux'
        ]);
    }

    public function create()
    {
        $types = [
            'paper' => 'Papier',
            'ink' => 'Encre',
            'plate' => 'Plaque',
            'chemical' => 'Chimique',
            'other' => 'Autre'
        ];
        
        $this->render('app', 'materials/create', [
            'title' => 'Ajouter un matériau',
            'types' => $types
        ]);
    }

    public function store()
    {
        $data = [
            'name' => trim($_POST['name']),
            'type' => $_POST['type'],
            'stock_quantity' => (float)$_POST['stock_quantity'],
            'unit' => trim($_POST['unit']),
            'min_stock_level' => !empty($_POST['min_stock_level']) ? (float)$_POST['min_stock_level'] : null,
            'cost_per_unit' => !empty($_POST['cost_per_unit']) ? (float)$_POST['cost_per_unit'] : null
        ];

        // Validation
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($data['type'])) {
            $errors[] = "Le type est obligatoire";
        }
        if (!isset($data['stock_quantity'])) {
            $errors[] = "Le stock est obligatoire";
        }
        if (empty($data['unit'])) {
            $errors[] = "L'unité est obligatoire";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo implode("\n", $errors);
            return;
        }

        Material::create($data);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Matériau créé avec succès'
        ];
        header('Location: ' . $this->basePath . '/material');
    }

    public function edit($id)
    {
        $material = Material::find($id);
        if (!$material) {
            http_response_code(404);
            echo "Matériau non trouvé";
            return;
        }

        $types = [
            'paper' => 'Papier',
            'ink' => 'Encre',
            'plate' => 'Plaque',
            'chemical' => 'Chimique',
            'other' => 'Autre'
        ];

        $this->render('app', 'materials/edit', [
            'material' => $material,
            'types' => $types,
            'title' => 'Modifier le matériau'
        ]);
    }

    public function update($id)
    {
        $material = Material::find($id);
        if (!$material) {
            http_response_code(404);
            echo "Matériau non trouvé";
            return;
        }

        $data = [
            'name' => trim($_POST['name']),
            'type' => $_POST['type'],
            'stock_quantity' => (float)$_POST['stock_quantity'],
            'unit' => trim($_POST['unit']),
            'min_stock_level' => !empty($_POST['min_stock_level']) ? (float)$_POST['min_stock_level'] : null,
            'cost_per_unit' => !empty($_POST['cost_per_unit']) ? (float)$_POST['cost_per_unit'] : null
        ];

        // Validation
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($data['type'])) {
            $errors[] = "Le type est obligatoire";
        }
        if (!isset($data['stock_quantity'])) {
            $errors[] = "Le stock est obligatoire";
        }
        if (empty($data['unit'])) {
            $errors[] = "L'unité est obligatoire";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo implode("\n", $errors);
            return;
        }

        $material->update($data);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Matériau mis à jour avec succès'
        ];
        header('Location: ' . $this->basePath . '/material');
    }

    public function delete($id)
    {
        $material = Material::find($id);
        if (!$material) {
            http_response_code(404);
            echo "Matériau non trouvé";
            return;
        }

        // Vérifier si le matériau est utilisé dans des commandes
        if ($material->orders()->count() > 0) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Impossible de supprimer: ce matériau est utilisé dans des commandes'
            ];
            header('Location: ' . $this->basePath . '/material');
            return;
        }

        $material->delete();
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Matériau supprimé avec succès'
        ];
        header('Location: ' . $this->basePath . '/material');
    }
}