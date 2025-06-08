<?php
namespace App\Controllers;

require_once __DIR__ . './../../vendor/autoload.php';

use App\Core\ViewRenderer;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Order;
use App\Models\ProductionStep;
use Exception;

use Illuminate\Database\Capsule\Manager as DB;
use Jump\JumpDataTable\DataAction;
use Jump\JumpDataTable\DataColumn;
use Jump\JumpDataTable\DataTable;
use Jump\JumpDataTable\Filter;
use Symfony\Component\Clock\now;

class OrderController
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
        $status = $_GET['status'] ?? null;
        $priority = $_GET['priority'] ?? null;

        $allowedSorts = ['id', 'reference', 'delivery_date', 'priority', 'status'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts))
            $sort = 'id';
        if (!in_array($direction, $allowedDirections))
            $direction = 'desc';

        $query = Order::with('customer');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $totalItems = $query->count();
        $offset = ($currentPage - 1) * $perPage;
        $orders = $query->orderBy($sort, $direction)
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->toArray();

        $table = DataTable::make()
            ->title('Liste des commandes')
            ->modelName('order')
            ->createUrl($this->basePath . '/order/create')
            ->publicUrl($this->basePath)
            ->addColumn((new DataColumn('id', 'ID'))->sortable())
            ->addColumn((new DataColumn('reference', 'Référence'))->searchable())
            ->addColumn((new DataColumn('customer.name', 'Client'))->searchable())
            ->addColumn((new DataColumn('delivery_date', 'Date livraison'))->sortable())
            ->addColumn((new DataColumn('priority', 'Priorité'))->sortable())
            ->addColumn((new DataColumn('status', 'Statut'))->sortable())
            ->addAction(DataAction::edit('Modifier', fn($item) => $this->basePath . '/order/' . 'edit/' . $item['id']))
            ->addAction(DataAction::delete('Supprimer', fn($item) => $this->basePath . '/order/' . 'delete/' . $item['id']))
            ->addAction(DataAction::view('Détails', fn($item) => $this->basePath . '/order/' . 'show/' . $item['id']))
            ->data($orders)
            ->addFilter(new Filter('status', 'Statut', [
                'received' => 'Reçue',
                'in_preparation' => 'En préparation',
                'in_printing' => 'En impression',
                'in_finishing' => 'En finition',
                'ready_for_delivery' => 'Prête à livrer',
                'delivered' => 'Livrée',
                'canceled' => 'Annulée'
            ]))
            ->addFilter(new Filter('priority', 'Priorité', [
                'low' => 'Basse',
                'medium' => 'Moyenne',
                'high' => 'Haute',
                'urgent' => 'Urgente'
            ]))
            ->enableRowSelection(true)
            ->setBulkActions([
                DataAction::delete('Supprimer', fn($item) => "/delete/{$item}"),
            ])
            ->paginate($totalItems, $perPage, $currentPage, $this->basePath . '/order', [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search,
                'status' => $status,
                'priority' => $priority
            ]);

        $this->render('app', 'orders/index', [
            'datatable' => $table->render(),
            'title' => 'Liste des commandes'
        ]);

    }

    public function create()
    {
        $customers = Customer::all();
        $materials = Material::all();

        $this->render('app', 'orders/create', [
            'title' => 'Créer une commande',
            'customers' => $customers,
            'materials' => $materials,
            'priorities' => ['low', 'medium', 'high', 'urgent']
        ]);
    }


    public function store()
    {
        $data = [
            'customer_id' => $_POST['customer_id'],
            'reference' => 'ORD-' . date('Ymd-His'),
            'delivery_date' => $_POST['delivery_date'],
            'priority' => $_POST['priority'],
            'notes' => $_POST['notes'] ?? null
        ];

        if (empty($data['customer_id'])) {
            http_response_code(400);
            echo "Le client est obligatoire";
            return;
        }

        DB::beginTransaction();

        try {
            $order = Order::create($data);

            $steps = ['prepress', 'printing', 'finishing', 'quality_check', 'packaging'];
            foreach ($steps as $step) {
                ProductionStep::create([
                    'order_id' => $order->id,
                    'step' => $step,
                    'status' => 'pending'
                ]);
            }

            if (!empty($_POST['materials'])) {
                foreach ($_POST['materials'] as $materialId => $quantity) {
                    if ($quantity > 0) {
                        $material = Material::find($materialId);

                        if (!$material) {
                            throw new \Exception("Matériau non trouvé");
                        }

                        if ($material->stock_quantity < $quantity) {
                            throw new \Exception("Stock insuffisant pour {$material->name}");
                        }

                        $material->decrement('stock_quantity', $quantity);
                        $order->materials()->attach($materialId, ['quantity_used' => $quantity]);
                    }
                }
            }

            DB::commit();


            Flash('message', 'Commande créée avec succès', 'success');

        } catch (\Exception $e) {
            DB::rollBack();


            Flash('message', 'Erreur : ' . $e->getMessage());

        }

        header('Location: ' . $this->basePath . '/order');
        exit;
    }
    public function show($id)
    {

        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de commande non fourni";
            return;
        }

        $order = Order::with([
            'customer',
            'files',
            'productionSteps' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'materials' => function ($query) {
                $query->select('materials.*', 'order_materials.quantity_used as pivot_quantity_used');
            }
        ])->findOrFail($id);

        $this->render('app', 'orders/show', [
            'order' => $order,
            'title' => 'Détails de la commande',
            'basePath' => $this->basePath
        ]);
    }


    public function edit($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de commande non fourni";
            return;
        }

        $order = Order::with('customer')->findOrFail($id);

        $materialsList = DB::table('materials')
            ->join('order_materials', 'materials.id', '=', 'order_materials.material_id')
            ->where('order_materials.order_id', $id)
            ->select('materials.*', 'order_materials.quantity_used')
            ->get();

        $customers = Customer::all();
        $allMaterials = Material::all();

        $this->render('app', 'orders/edit', [
            'order' => $order,
            'materialsList' => $materialsList,
            'customers' => $customers,
            'materials' => $allMaterials,
            'priorities' => ['low', 'medium', 'high', 'urgent'],
            'statuses' => ['received', 'in_preparation', 'in_printing', 'in_finishing', 'ready_for_delivery', 'delivered', 'canceled'],
            'title' => 'Modifier la commande'
        ]);
    }

    public function update($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de commande non fourni";
            return;
        }
        $order = Order::find($id);

        if (!$order) {
            http_response_code(404);
            echo "Commande non trouvée";
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($order->materials as $material) {
                $material->increment('stock_quantity', $material->pivot->quantity_used);
            }
            $order->update([
                'customer_id' => $_POST['customer_id'],
                'delivery_date' => $_POST['delivery_date'],
                'priority' => $_POST['priority'],
                'status' => $_POST['status'],
                'notes' => $_POST['notes'] ?? null
            ]);

            $order->materials()->detach();
            if (!empty($_POST['materials'])) {
                foreach ($_POST['materials'] as $materialId => $quantity) {
                    if ($quantity > 0) {
                        $material = Material::find($materialId);

                        if (!$material) {
                            throw new \Exception("Matériau non trouvé");
                        }

                        if ($material->stock_quantity < $quantity) {
                            throw new \Exception("Stock insuffisant pour {$material->name}");
                        }

                        $material->decrement('stock_quantity', $quantity);
                        $order->materials()->attach($materialId, ['quantity_used' => $quantity]);
                    }
                }
            }

            DB::commit();


            Flash('message', 'Commande mise à jour  avec succès', 'success');

        } catch (\Exception $e) {
            DB::rollBack();

            Flash('message', 'Erreur : ' . $e->getMessage());

        }

        header('Location: ' . $this->basePath . '/order');
        exit;
    }

    public function delete($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de commande non fourni";
            return;
        }

        DB::beginTransaction();

        try {
            $order = Order::find($id);

            if (!$order) {
                throw new \Exception("Commande non trouvée");
            }

            foreach ($order->materials as $material) {
                $material->increment('stock_quantity', $material->pivot->quantity_used);
            }

            $order->delete();

            DB::commit();


            Flash('message', 'Commande supprimée avec succès', 'success');

        } catch (\Exception $e) {
            DB::rollBack();

            Flash('message', 'Erreur : ' . $e->getMessage());

        }

        header('Location: ' . $this->basePath . '/order');
        exit;
    }
    public function updateStepStatus($orderId, $stepId)
    {
        $step = ProductionStep::where('id', $stepId)
            ->where('order_id', $orderId)
            ->first();

        if (!$step) {
            http_response_code(404);
            echo "Étape de production non trouvée";
            return;
        }

        $status = $_POST['status'];
        $updateData = ['status' => $status];

        if ($status === 'in_progress') {
            $updateData['start_time'] = date('Y-m-d H:i:s');
        } elseif ($status === 'completed') {
            ;
        } elseif ($status === 'completed') {
            $updateData['end_time'] = date('Y-m-d H:i:s');
        } elseif ($status === 'completed') {
            ;
        }

        $step->update($updateData);

        $this->updateOrderStatus($orderId);

        echo json_encode(['success' => true]);
    }

    protected function updateOrderStatus($orderId)
    {
        $order = Order::with('productionSteps')->find($orderId);

        if ($order->status === 'canceled') {
            return;
        }

        $steps = $order->productionSteps;
        $allCompleted = true;
        $hasProblems = false;

        foreach ($steps as $step) {
            if ($step->status !== 'completed') {
                $allCompleted = false;
            }
            if ($step->status === 'failed') {
                $hasProblems = true;
            }
        }

        if ($hasProblems) {
            $order->update(['status' => 'on_hold']);
        } elseif ($allCompleted) {
            $order->update(['status' => 'ready_for_delivery']);
        } else {
            $stepOrder = ['prepress', 'printing', 'finishing', 'quality_check', 'packaging'];
            $currentStepIndex = 0;

            foreach ($steps as $step) {
                if ($step->status === 'in_progress' || $step->status === 'completed') {
                    $index = array_search($step->step, $stepOrder);
                    if ($index > $currentStepIndex) {
                        $currentStepIndex = $index;
                    }
                }
            }

            $statusMap = [
                0 => 'in_preparation',
                1 => 'in_printing',
                2 => 'in_finishing',
                3 => 'quality_check',
                4 => 'ready_for_delivery'
            ];

            $order->update(['status' => $statusMap[$currentStepIndex]]);
        }
    }

    public function productionTracking($orderId)
    {
        if (is_array($orderId)) {
            $orderId = $orderId['id'] ?? null;
        }

        if (!$orderId) {
            http_response_code(400);
            echo "ID de commande non fourni";
            return;
        }

        $order = Order::with([
            'productionSteps' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])->find($orderId);

        if (!$order) {
            http_response_code(404);
            echo "Commande non trouvée";
            return;
        }

        $this->render('app', 'orders/production_tracking', [
            'order' => $order,
            'title' => 'Suivi de production'
        ]);
    }
}