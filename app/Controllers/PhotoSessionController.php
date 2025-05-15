<?php
namespace App\Controllers;

use App\Models\Photo;
use App\Models\Customer;
use App\Core\ViewRenderer;
use Illuminate\Support\Str;
use App\Models\PhotoSession;
use Illuminate\Support\Facades\DB;

class PhotoSessionController
{
    use ViewRenderer;
    protected $basePath;
    protected $photosBaseDir;

    public function __construct()
    {
        $this->basePath = '/Projets/KongB/public';
        $this->photosBaseDir = '/var/www/html/Projets/KongB/public/photos/';
    }

    // Liste des séances
    public function index()
    {
        $perPage = 10;
        $sort = $_GET['sort'] ?? 'date';
        $direction = $_GET['direction'] ?? 'desc';
        $search = $_GET['search'] ?? '';

        $allowedSorts = ['date', 'type', 'status'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts))
            $sort = 'date';
        if (!in_array($direction, $allowedDirections))
            $direction = 'desc';

        $query = PhotoSession::with('customer')
            ->orderBy($sort, $direction);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $sessions = $query->paginate($perPage);

        $this->render('app', 'photo_sessions/index', [
            'sessions' => $sessions,
            'title' => 'Liste des séances',
            'sort' => $sort,
            'direction' => $direction,
            'search' => $search,
        ]);
    }

    // Formulaire de création
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $this->render('app', 'photo_sessions/create', [
            'title' => 'Nouvelle séance photo',
            'customers' => $customers,
            'sessionTypes' => ['Portrait', 'Mariage', 'Famille', 'Grossesse', 'Bébé', 'Produit', 'Autre']
        ]);
    }

    // Enregistrement d'une nouvelle séance
    public function store()
    {
        $data = $this->validateSessionData($_POST);
        $customer = $this->getOrCreatecustomer($data);

        // Create session directory
        $sessionDir = $this->createSessionDirectory($customer->name);

        // Verify directory was created
        $fullPath = $this->photosBaseDir . basename($sessionDir);
        if (!is_dir($fullPath)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Échec de création du dossier: ' . $fullPath
            ];
            header('Location: ' . $this->basePath . '/photo-session/create');
            return;
        }

        // Create session record
        $session = PhotoSession::create([
            'type' => $data['type'],
            'date' => $data['date'],
            'notes' => $data['notes'] ?? null,
            'customer_id' => $customer->id,
            'directory_path' => $sessionDir
        ]);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Séance créée avec succès'
        ];
        header('Location: ' . $this->basePath . '/photo-session/' . $session->id);
    }

    // Détails d'une séance

    public function show($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de séance non fourni";
            return;
        }

        // Chargez la session avec les photos et le client
        $session = PhotoSession::with(['photos', 'customer'])->find($id);

        if (!$session) {
            http_response_code(404);
            echo "Séance non trouvée";
            return;
        }

        // Vérifiez si le client est chargé
        if (!isset($session->customer)) {
            $session->customer = Customer::find($session->customer_id);
        }

        // Récupérez les photos depuis la relation
        $photos = $session->photos;

        // Compatibilité avec l'ancien système (photos dans le dossier)
        $dirPath = $this->photosBaseDir . basename($session->directory_path);
        if (is_dir($dirPath)) {
            $files = scandir($dirPath);
            foreach ($files as $file) {
                if (!in_array($file, ['.', '..'])) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        // Si la photo n'existe pas en base, l'ajouter
                        if (!$photos->contains('filename', $file)) {
                            Photo::create([
                                'filename' => $file,
                                'path' => 'photos/' . basename($session->directory_path) . '/' . $file,
                                'session_id' => $session->id,
                                'customer_id' => $session->customer_id
                            ]);
                        }
                    }
                }
            }
            // Recharger les photos après éventuels ajouts
            $photos = $session->fresh()->photos;
        }

        $this->render('app', 'photo_sessions/show', [
            'session' => $session,
            'photos' => $photos,
            'title' => 'Détails de la séance',
            'baseUrl' => $this->basePath
        ]);
    }
    public function storePhotos($id)
    {
        // Gestion du cas où $id est un tableau (provenant du routeur)
        if (is_array($id)) {
            $id = $id['id'] ?? null;
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de séance non fourni";
            return;
        }

        // Récupération de la session avec vérification d'existence
        $session = PhotoSession::find($id);

        if (!$session) {
            http_response_code(404);
            echo "Séance non trouvée";
            return;
        }

        // Vérification des fichiers uploadés
        if (empty($_FILES['photos'])) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Aucun fichier sélectionné'
            ];
            header('Location: ' . $this->basePath . '/photo-session/' . $id);
            return;
        }

        $dirPath = $this->photosBaseDir . basename($session->directory_path);
        $uploadedFiles = [];

        // Création du dossier si inexistant
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        // Traitement de chaque fichier
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $originalName = basename($_FILES['photos']['name'][$key]);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $newName = Str::random(20) . '.' . $extension;
                    $relativePath = 'photos/' . basename($session->directory_path) . '/' . $newName;
                    $destination = $dirPath . '/' . $newName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        // Enregistrement en base de données
                        Photo::create([
                            'filename' => $newName,
                            'path' => $relativePath,
                            'original_name' => $originalName,
                            'file_size' => $_FILES['photos']['size'][$key],
                            'mime_type' => $_FILES['photos']['type'][$key],
                            'session_id' => $session->id,
                            'customer_id' => $session->customer_id
                        ]);

                        $uploadedFiles[] = $newName;
                    }
                }
            }
        }

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => count($uploadedFiles) . ' photo(s) ajoutée(s) avec succès'
        ];
        header('Location: ' . $this->basePath . '/photo-session/' . $id);
    }
 public function deletePhoto($params)
{
    // Gestion du cas où les paramètres sont passés sous forme de tableau
    if (is_array($params)) {
        $id = $params['id'] ?? null;
        $photoId = $params['photoId'] ?? null;
    } else {
        // Pour compatibilité avec l'ancien système si nécessaire
        $args = func_get_args();
        $id = $args[0] ?? null;
        $photoId = $args[1] ?? null;
    }

    if (!$id || !$photoId) {
        http_response_code(400);
        echo "Paramètres manquants";
        return;
    }

    // Récupération de la photo avec vérification
    $photo = Photo::where('session_id', $id)->find($photoId);
    
    if (!$photo) {
        http_response_code(404);
        echo "Photo non trouvée";
        return;
    }

    // Suppression du fichier physique
    $filePath = $this->photosBaseDir . basename($photo->session->directory_path) . '/' . $photo->filename;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Suppression de l'enregistrement en base
    $photo->delete();

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Photo supprimée avec succès'
    ];
    header('Location: ' . $this->basePath . '/photo-session/' . $id);
}
    // Formulaire d'ajout de photos
    public function addPhotos($id)
    {
        $session = PhotoSession::find($id);

        if (!$session) {
            http_response_code(404);
            echo "Séance non trouvée";
            return;
        }

        $this->render('app', 'photo_sessions/add-photos', [
            'session' => $session,
            'title' => 'Ajouter des photos'
        ]);
    }


    // Suppression d'une séance
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $session = PhotoSession::find($id);

            if (!$session) {
                http_response_code(404);
                echo "Séance non trouvée";
                return;
            }

            // Suppression du dossier
            $dirPath = $this->photosBaseDir . basename($session->directory_path);
            if (is_dir($dirPath)) {
                $this->rrmdir($dirPath);
            }

            // Suppression en base
            $session->delete();
        });

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Séance supprimée avec succès'
        ];
        header('Location: ' . $this->basePath . '/photo-session');
    }
public function updateStatus($id)
{
    if (is_array($id)) {
        $id = $id['id'] ?? null;
    }

    if (!$id) {
        http_response_code(400);
        echo "ID de séance non fourni";
        return;
    }

    $session = PhotoSession::find($id);
    
    if (!$session) {
        http_response_code(404);
        echo "Séance non trouvée";
        return;
    }

    $newStatus = $_POST['status'] ?? null;
    $allowedStatuses = array_keys(PhotoSession::getStatuses());

    if (!in_array($newStatus, $allowedStatuses)) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Statut invalide'
        ];
        header('Location: ' . $this->basePath . '/photo-session/' . $id);
        return;
    }

    $session->status = $newStatus;
    $session->save();

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Statut mis à jour avec succès'
    ];
    header('Location: ' . $this->basePath . '/photo-session/' . $id);
}
    // Formulaire de modification
    public function edit($id)
    {
        if (is_array($id)) {
            $id = $id['id'] ?? null; // Extract ID from array if accidentally passed
        }

        if (!$id) {
            http_response_code(400);
            echo "ID de séance non fourni";
            return;
        }

        $session = PhotoSession::find($id);

        if (!$session) {
            http_response_code(404);
            echo "Séance non trouvée";
            return;
        }

        if (!isset($session->customer)) {
            $session->customer = Customer::find($session->customer_id);
        }

        $customers = Customer::orderBy('name')->get();
        $this->render('app', 'photo_sessions/edit', [
            'session' => $session,
            'customers' => $customers,
            'sessionTypes' => ['Portrait', 'Mariage', 'Famille', 'Grossesse', 'Bébé', 'Produit', 'Autre'],
            'title' => 'Modifier la séance'
        ]);
    }

    // Méthodes utilitaires
    protected function validateSessionData($data)
    {
        $required = ['type', 'date', 'customer_id'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo "Le champ $field est obligatoire";
                exit;
            }
        }

        return [
            'type' => trim($data['type']),
            'date' => trim($data['date']),
            'notes' => trim($data['notes'] ?? ''),
            'customer_id' => (int) $data['customer_id'],
            'new_customer_name' => trim($data['new_customer_name'] ?? ''),
            'new_customer_phone' => trim($data['new_customer_phone'] ?? '')
        ];
    }

    protected function getOrCreatecustomer($data)
    {
        if ($data['customer_id'] === 'new' && !empty($data['new_customer_name'])) {
            // Création d'un nouveau customer
            return Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone']
            ]);
        } else {
            // customer existant
            return Customer::findOrFail($data['customer_id']);
        }
    }
    public function createSessionDirectory($customername = null): string
    {
        // Generate directory name
        $dirName = 'seance_' . date('Ymd_His') . '_' .
            ($customername ? Str::slug($customername, '_') : Str::random(3));

        $fullPath = $this->photosBaseDir . $dirName;
        if (!is_dir($fullPath)) {
            try {
                if (!mkdir($fullPath, 0775, true)) {
                    throw new \RuntimeException(
                        "Échec de création du dossier: {$fullPath}\n" .
                        "Vérifiez que:\n" .
                        "1. Le dossier $this->photosBaseDir existe\n" .
                        "2. L'utilisateur 'daemon' a les permissions d'écriture\n" .
                        "3. Les permissions du dossier parent sont correctes (755)"
                    );
                }
                chown($fullPath, 'daemon');
                chgrp($fullPath, 'daemon');
            } catch (\Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }

        return 'photos/' . $dirName;
    }
    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }


}