<?php
namespace App\Controllers;

require_once __DIR__ . './../../vendor/autoload.php';

use App\Models\ProductionStep;
use App\Models\Order;
use App\Core\ViewRenderer;

class ProductionStepController
{
    use ViewRenderer;
    protected $basePath;

    public function __construct()
    {
        $this->basePath = '/Projets/autres/hiernostine/public';
    }

    public function updateStatus($orderId, $stepId)
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
        $updateData = [
            'status' => $status,
            'assigned_to' => $_POST['assigned_to'] ?? $step->assigned_to
        ];

        // Gestion des dates
        switch ($status) {
            case 'in_progress':
                if (!$step->start_time) {
                    $updateData['start_time'] = date('Y-m-d H:i:s');
                }
                break;
                
            case 'completed':
                $updateData['end_time'] = date('Y-m-d H:i:s');
                break;
                
            case 'on_hold':
                $updateData['comments'] = $_POST['comments'] ?? 'Mis en pause';
                break;
        }

        $step->update($updateData);

        // Mettre à jour le statut global de la commande
        $this->updateOrderStatus($orderId);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Statut de l\'étape mis à jour'
        ];
        header("Location: {$this->basePath}/order/show/{$orderId}");
    }

    protected function updateOrderStatus($orderId)
    {
        $order = Order::with('productionSteps')->find($orderId);
        
        if ($order->status === 'canceled') {
            return; // Ne pas modifier si annulée
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
            $newStatus = 'on_hold';
        } elseif ($allCompleted) {
            $newStatus = 'ready_for_delivery';
        } else {
            // Déterminer l'étape la plus avancée
            $stepStatuses = [
                'prepress' => 'in_preparation',
                'printing' => 'in_printing',
                'finishing' => 'in_finishing'
            ];
            
            $currentStatus = $order->status;
            foreach ($stepStatuses as $step => $status) {
                $step = $steps->where('step', $step)->first();
                if ($step && ($step->status === 'in_progress' || $step->status === 'completed')) {
                    $currentStatus = $status;
                }
            }
            $newStatus = $currentStatus;
        }

        $order->update(['status' => $newStatus]);
    }

    public function create($orderId)
    {
          if (is_array($orderId)) {
            $orderId = $orderId['order_id'] ?? null; // Extract ID from array if accidentally passed
        }

        if (!$orderId) {
            http_response_code(400);
            echo "ID de la commande non fourni";
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            http_response_code(404);
            echo "Commande non trouvée";
            return;
        }

        $this->render('app', 'production_steps/create', [
            'order' => $order,
            'title' => 'Ajouter une étape de production'
        ]);
    }

    public function store($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            http_response_code(404);
            echo "Commande non trouvée";
            return;
        }

        $data = [
            'order_id' => $orderId,
            'step' => $_POST['step'],
            'status' => 'pending',
            'comments' => $_POST['comments'] ?? null
        ];

        ProductionStep::create($data);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Étape ajoutée avec succès'
        ];
        header("Location: {$this->basePath}/order/show/{$orderId}");
    }
}