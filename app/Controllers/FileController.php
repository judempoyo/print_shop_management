<?php
namespace App\Controllers;

require_once __DIR__ . './../../vendor/autoload.php';

use App\Models\File;
use App\Models\Order;
use App\Core\ViewRenderer;

class FileController
{
    use ViewRenderer;
    protected $basePath;

    public function __construct()
    {
        $this->basePath = '/Projets/autres/hiernostine/public';
    }

    public function upload($orderId)
    {
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
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            http_response_code(500);
            echo "Erreur lors du téléchargement du fichier";
            return;
        }

        File::create([
            'order_id' => $orderId,
            'file_name' => $file['name'],
            'file_path' => 'orders/' . $fileName,
            'file_type' => $file['type'],
            'file_size' => $file['size']
        ]);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Fichier téléchargé avec succès'
        ];
        header("Location: {$this->basePath}/order/show/{$orderId}");
    }

    public function delete($id)
    {
        $file = File::find($id);
        
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
        header("Location: {$this->basePath}/order/show/{$orderId}");
    }
}