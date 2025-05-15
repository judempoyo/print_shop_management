<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoManager
{
    private $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    function storePhoto($photo)
{
    $imageName = time() . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
    move_uploaded_file($photo['tmp_name'], 'photos/' . $imageName);
    return $imageName;
}
}