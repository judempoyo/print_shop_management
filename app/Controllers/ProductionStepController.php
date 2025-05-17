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

    public function edit(array $params)
    {
        // Extraire les IDs du tableau de paramètres
        $orderId = $params['order_id'] ?? null;
        $stepId = $params['id'] ?? null;

        if (!$orderId || !$stepId) {
            http_response_code(400);
            echo "IDs de commande et étape requis";
            return;
        }

        $step = ProductionStep::where('id', $stepId)
            ->where('order_id', $orderId)
            ->first();

        if (!$step) {
            http_response_code(404);
            echo "Étape de production non trouvée";
            return;
        }

        $order = $step->order;

        $this->render('app', 'production_steps/edit', [
            'order' => $order,
            'step' => $step,
            'statusOptions' => [
                'pending' => 'En attente',
                'in_progress' => 'En cours',
                'completed' => 'Terminé',
                'on_hold' => 'En pause',
                'failed' => 'Échec'
            ],
            'title' => 'Modifier l\'étape de production'
        ]);
    }
    protected function updateOrderStatus($orderId)
    {
        $order = Order::with([
            'productionSteps' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])->findOrFail($orderId);

        if ($order->status === 'canceled') {
            return;
        }

        // Définir le flux des étapes
        $stepFlow = [
            'prepress' => 'in_preparation',
            'printing' => 'in_printing',
            'finishing' => 'in_finishing',
            'quality_check' => 'quality_check',
            'packaging' => 'packaging'
        ];

        // 1. Vérifier s'il y a des étapes en échec
        if ($order->productionSteps->where('status', 'failed')->count() > 0) {
            $order->update(['status' => 'on_hold']);
            return;
        }

        // 2. Vérifier si toutes les étapes sont terminées
        if (
            $order->productionSteps->every(function ($step) {
                return $step->status === 'completed';
            })
        ) {
            $order->update(['status' => 'ready_for_delivery']);
            return;
        }

        // 3. Déterminer l'étape la plus avancée
        $currentStatus = $order->status;
        $foundActive = false;

        foreach ($stepFlow as $stepName => $statusValue) {
            $step = $order->productionSteps->where('step', $stepName)->first();

            if ($step) {
                if ($step->status === 'in_progress') {
                    $currentStatus = $statusValue;
                    $foundActive = true;
                } elseif ($step->status === 'completed' && !$foundActive) {
                    $currentStatus = $statusValue;
                }
            }
        }

        // Cas spécial si l'étape actuelle est terminée mais pas la suivante
        if (
            in_array($currentStatus, ['in_preparation', 'in_printing', 'in_finishing']) &&
            !$this->hasActiveSteps($order, $currentStatus, $stepFlow)
        ) {
            $currentStatus = $this->getNextPendingStatus($order, $currentStatus, $stepFlow);
        }

        $order->update(['status' => $currentStatus]);
    }

    protected function hasActiveSteps(Order $order, string $currentStatus, array $stepFlow): bool
    {
        $statusFlow = [
            'in_preparation' => ['in_preparation'],
            'in_printing' => ['in_printing', 'in_preparation'],
            'in_finishing' => ['in_finishing', 'in_printing', 'in_preparation']
        ];

        if (!isset($statusFlow[$currentStatus])) {
            return false;
        }

        foreach ($statusFlow[$currentStatus] as $status) {
            $stepName = array_search($status, $stepFlow);
            if (
                $order->productionSteps->where('status', 'in_progress')
                    ->where('step', $stepName)
                    ->count() > 0
            ) {
                return true;
            }
        }

        return false;
    }

    protected function getNextPendingStatus(Order $order, string $currentStatus, array $stepFlow): string
    {
        $statusOrder = [
            'in_preparation',
            'in_printing',
            'in_finishing',
            'quality_check',
            'packaging'
        ];

        $currentIndex = array_search($currentStatus, $statusOrder);
        if ($currentIndex === false) {
            return $currentStatus;
        }

        for ($i = $currentIndex + 1; $i < count($statusOrder); $i++) {
            $stepName = array_search($statusOrder[$i], $stepFlow);
            $step = $order->productionSteps->where('step', $stepName)->first();

            if ($step && $step->status !== 'completed') {
                return $statusOrder[$i];
            }
        }

        return 'ready_for_delivery';
    }
    protected function validateIds(array $params): array
    {
        $orderId = $params['order_id'] ?? null;
        $stepId = $params['id'] ?? null;

        if (!$orderId || !$stepId) {
            throw new \InvalidArgumentException("IDs de commande et étape requis");
        }

        return [(int) $orderId, (int) $stepId];
    }

    protected function getProductionStep(int $orderId, int $stepId): ProductionStep
    {
        $step = ProductionStep::where('id', $stepId)
            ->where('order_id', $orderId)
            ->first();

        if (!$step) {
            throw new \InvalidArgumentException("Étape de production non trouvée");
        }

        return $step;
    }

    protected function validateStepData(array $data): array
    {
        $validated = [
            'status' => $data['status'] ?? null,
            'assigned_to' => trim($data['assigned_to'] ?? ''),
            'comments' => trim($data['comments'] ?? ''),
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null
        ];

        if (!in_array($validated['status'], ['pending', 'in_progress', 'completed', 'on_hold', 'failed'])) {
            throw new \InvalidArgumentException("Statut invalide");
        }

        return $validated;
    }

    protected function prepareUpdateData(ProductionStep $step, array $validatedData): array
    {
        $updateData = [
            'status' => $validatedData['status'],
            'assigned_to' => $validatedData['assigned_to'],
            'comments' => $validatedData['comments']
        ];

        // Gestion des dates automatiques
        switch ($validatedData['status']) {
            case 'in_progress':
                if (!$step->start_time) {
                    $updateData['start_time'] = date('Y-m-d H:i:s');
                }
                break;

            case 'completed':
                $updateData['end_time'] = date('Y-m-d H:i:s');
                break;

            case 'failed':
                $updateData['comments'] = $validatedData['comments'] ?: 'Échec non spécifié';
                break;
        }

        // Gestion des dates manuelles
        if ($validatedData['start_time']) {
            $updateData['start_time'] = $validatedData['start_time'];
        }
        if ($validatedData['end_time']) {
            $updateData['end_time'] = $validatedData['end_time'];
        }

        return $updateData;
    }


    public function delete(array $params)
    {
        try {
            // Validation des paramètres
            [$orderId, $stepId] = $this->validateIds($params);

            // Récupération et vérification de l'étape
            $step = $this->getProductionStep($orderId, $stepId);

            // Vérification des préconditions
            $this->validateDeletionConditions($step);

            // Suppression
            $step->delete();

            // Mise à jour du statut global
            $this->updateOrderStatus($orderId);


            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Étape supprimée avec succès'
            ];

        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression'
            ];
        }

        header("Location: {$this->basePath}/order/show/{$orderId}");
        exit;
    }

    protected function validateDeletionConditions(ProductionStep $step): void
    {
        if ($step->order->status === 'completed') {
            throw new \InvalidArgumentException(
                'Impossible de supprimer une étape d\'une commande terminée'
            );
        }

    }
}