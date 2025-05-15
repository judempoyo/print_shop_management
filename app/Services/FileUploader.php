<?php
namespace App\Services;

class FileUploader
{
    protected $uploadDir;
    protected $thumbnailsDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/';
        $this->thumbnailsDir = __DIR__ . '/../../public/uploads/thumbnails/';
        
        // Créer les répertoires s'ils n'existent pas
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        if (!file_exists($this->thumbnailsDir)) {
            mkdir($this->thumbnailsDir, 0777, true);
        }
    }

    public function uploadMultiple(array $files, array $options = [])
    {
        $uploadedFiles = [];
        
        if (is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                $fileData = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                if ($fileData['error'] === UPLOAD_ERR_OK) {
                    $uploadedFiles[] = $this->upload($fileData, $options);
                }
            }
        }
        
        return $uploadedFiles;
    }

    public function upload(array $file, array $options = [])
    {
        // Validation du type
        if (isset($options['allowed_types']) && !in_array($file['type'], $options['allowed_types'])) {
            throw new \Exception("Type de fichier non autorisé");
        }
        
        // Validation de la taille
        if (isset($options['max_size']) && $file['size'] > $options['max_size']) {
            throw new \Exception("Fichier trop volumineux");
        }
        
        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'path' => '/uploads/' . $filename,
                'size' => $file['size'],
                'type' => $file['type'],
                'name' => $file['name']
            ];
        }
        
        throw new \Exception("Erreur lors de l'upload du fichier");
    }

    public function createThumbnail($imagePath, $width)
    {
        $fullPath = __DIR__ . '/../../public' . $imagePath;
        $info = getimagesize($fullPath);
        
        switch ($info['mime']) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($fullPath);
                break;
            default:
                return null;
        }
        
        $ratio = $width / $info[0];
        $height = $info[1] * $ratio;
        
        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        
        $thumbFilename = 'thumb_' . basename($imagePath);
        $thumbPath = $this->thumbnailsDir . $thumbFilename;
        
        switch ($info['mime']) {
            case 'image/jpeg':
                imagejpeg($thumb, $thumbPath);
                break;
            case 'image/png':
                imagepng($thumb, $thumbPath);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($thumb);
        
        return '/uploads/thumbnails/' . $thumbFilename;
    }
}