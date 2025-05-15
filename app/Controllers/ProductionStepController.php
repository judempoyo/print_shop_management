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
        $updateData = ['status' => $status];

        if ($status === 'in_progress') {
            $updateData['start_time'] = now();
            $updateData['assigned_to'] = $_POST['assigned_to'] ?? null;
        } elseif ($status === 'completed') {
            $updateData['end_time'] = now();
        }

        $step->update($updateData);

        // Update order status
        $order = Order::find($orderId);
        $this->updateOverallOrderStatus($order);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Statut de l\'étape mis à jour'
        ];
        header("Location: {$this->basePath}/order/show/{$orderId}");
    }

    protected function updateOverallOrderStatus($order)
    {
        // Similar logic as in OrderController
        // Can be moved to a service class if used in multiple places
    }
}