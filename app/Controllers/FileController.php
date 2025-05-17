<?php
namespace App\Controllers;

require_once __DIR__ . './../../vendor/autoload.php';

use App\Models\File;
use App\Models\Order;
use App\Core\ViewRenderer;
use Jump\JumpDataTable\DataTable;
use Jump\JumpDataTable\DataAction;
use Jump\JumpDataTable\DataColumn;

class FileController
{
    use ViewRenderer;
    protected $basePath;

    public function __construct()
    {
        $this->basePath = '/Projets/autres/hiernostine/public';
    }

    public function showUploadForm($orderId)
    {
        if (is_array($orderId)) {
        $orderId = $orderId['order_id'] ?? null;
    }

    if (!$orderId) {
        http_response_code(400);
        echo "ID de commande non fourni";
        return;
    }

        $order = Order::find($orderId);
        
        if (!$order) {
            http_response_code(404);
            echo "Commande non trouvée";
            return;
        }

        $this->render('app', 'files/upload', [
            'order' => $order,
            'title' => 'Ajouter un fichier'
        ]);
    }

    public function listFiles($orderId)
    {
         if (is_array($orderId)) {
        $orderId = $orderId['order_id'] ?? null;
    }

    if (!$orderId) {
        http_response_code(400);
        echo "ID de commande non fourni";
        return;
    }

        $order = Order::with('files')->find($orderId);
        
        if (!$order) {
            http_response_code(404);
            echo "Commande non trouvée";
            return;
        }

        $perPage = 10;
        $currentPage = $_GET['page'] ?? 1;
        $sort = $_GET['sort'] ?? 'id';
        $direction = $_GET['direction'] ?? 'desc';
        $search = $_GET['search'] ?? '';

        $allowedSorts = ['id', 'file_name', 'created_at'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) $sort = 'id';
        if (!in_array($direction, $allowedDirections)) $direction = 'desc';

        $query = File::where('order_id', $orderId);

        if (!empty($search)) {
            $query->where('file_name', 'LIKE', "%{$search}%");
        }

        $totalItems = $query->count();
        $offset = ($currentPage - 1) * $perPage;
        $files = $query->orderBy($sort, $direction)
                      ->offset($offset)
                      ->limit($perPage)
                      ->get()
                      ->toArray();

        $table = DataTable::make()
            ->title('Fichiers de la commande #' . $order->reference)
            ->modelName('file')
            ->createUrl($this->basePath.'/file/upload/'.$orderId)
            ->publicUrl($this->basePath)
            ->addColumn((new DataColumn('id', 'ID'))->sortable())
            ->addColumn((new DataColumn('file_name', 'Nom du fichier'))->searchable())
            ->addColumn((new DataColumn('file_type', 'Type'))->sortable())
            ->addColumn((new DataColumn('file_size', 'Taille (Ko)')))
            ->addColumn((new DataColumn('created_at', 'Date d\'ajout'))->sortable())
            ->addAction(DataAction::view('Télécharger', fn($item) => $this->basePath.'/file/download/'.$item['id']))
            ->addAction(DataAction::delete('Supprimer', fn($item) => $this->basePath.'/file/delete/'.$item['id']))
            ->data($files)
            ->paginate($totalItems, $perPage, $currentPage, $this->basePath.'/file/list/'.$orderId, [
                'sort' => $sort,
                'direction' => $direction,
                'search' => $search
            ]);

        $this->render('app', 'files/index', [
            'datatable' => $table->render(),
            'order' => $order,
            'title' => 'Fichiers de la commande'
        ]);
    }

  public function upload($orderId)
{
     if (is_array($orderId)) {
        $orderId = $orderId['order_id'] ?? null;
    }

    if (!$orderId) {
        http_response_code(400);
        echo "ID de commande non fourni";
        return;
    }
    $order = Order::find($orderId);
    
    if (!$order) {
        http_response_code(404);
        echo "Commande non trouvée";
        return;
    }

    if (empty($_FILES['file'])) {
        http_response_code(400);
        echo "Aucun fichier téléchargé";
        return;
    }

    $file = $_FILES['file'];
    $uploadDir = __DIR__ . '/../../public/uploads/orders/';
    
    // Chemin absolu corrigé
    $uploadDir = realpath(__DIR__ . '/../../public/') . '/uploads/orders/';
    
    // Créer le dossier avec permissions appropriées
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Impossible de créer le dossier: " . $uploadDir);
            http_response_code(500);
            echo "Erreur serveur: impossible de créer le dossier de destination";
            return;
        }
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        error_log("Erreur déplacement fichier: " . $file['tmp_name'] . " vers " . $targetPath);
        error_log("Dernière erreur PHP: " . json_encode(error_get_last()));
        http_response_code(500);
        echo "Erreur lors du téléchargement du fichier. Vérifiez les permissions.";
        return;
    }

    // Vérifier que le fichier a bien été créé
    if (!file_exists($targetPath)) {
        http_response_code(500);
        echo "Erreur: le fichier n'a pas pu être enregistré";
        return;
    }

    File::create([
        'order_id' => $orderId,
        'file_name' => $file['name'],
        'file_path' => 'uploads/orders/' . $fileName,
        'file_type' => $file['type'],
        'file_size' => $file['size']
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Fichier téléchargé avec succès'
    ];
    header("Location: {$this->basePath}/file/list/{$orderId}");
}    public function download($fileId)
    {
         if (is_array($fileId)) {
        $fileId = $fileId['id'] ?? null;
    }

    if (!$fileId) {
        http_response_code(400);
        echo "ID de commande non fourni";
        return;
    }
        $file = File::find($fileId);
        
        if (!$file) {
            http_response_code(404);
            echo "Fichier non trouvé";
            return;
        }

        $filePath = __DIR__ . '/../../public/uploads/' . $file->file_path;
        
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "Fichier introuvable sur le serveur";
            return;
        }

        header('Content-Type: ' . mime_content_type($filePath));
        header('Content-Disposition: attachment; filename="' . $file->file_name . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function delete($fileId)
    {
        $file = File::find($fileId);
        
        if (!$file) {
            http_response_code(404);
            echo "Fichier non trouvé";
            return;
        }

        $filePath = __DIR__ . '/../../public/uploads/' . $file->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $orderId = $file->order_id;
        $file->delete();

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Fichier supprimé avec succès'
        ];
        header("Location: {$this->basePath}/file/list/{$orderId}");
    }
}