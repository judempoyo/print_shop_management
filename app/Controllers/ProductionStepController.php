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

    /* public function updateStatus($orderId, $stepId)
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
    } */

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
    public function updateStatus(array $params)
    {
        try {
            // Validation des paramètres
            [$orderId, $stepId] = $this->validateIds($params);

            // Récupération et vérification de l'étape
            $step = $this->getProductionStep($orderId, $stepId);

            // Validation des données du formulaire
            $validatedData = $this->validateStepData($_POST);

            // Préparation des données de mise à jour
            $updateData = $this->prepareUpdateData($step, $validatedData);

            // Mise à jour de l'étape
            $step->update($updateData);

            // Mise à jour du statut global de la commande
            $this->updateOrderStatus($orderId);


            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Étape mise à jour avec succès'
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
                'message' => 'Une erreur est survenue lors de la mise à jour'
            ];
        }

        header("Location: {$this->basePath}/order/show/{$orderId}");
        exit;
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