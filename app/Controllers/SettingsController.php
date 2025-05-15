<?php
namespace App\Controllers;

use App\Services\SettingsService;
use App\Core\ViewRenderer;

class SettingsController
{
  use ViewRenderer;

  protected $basePath;

  public function __construct()
  {
    $this->basePath = '/Projets/autres/hiernostine/public';
  }

  public function index()
  {
    $settings = [
      'session_prices' => SettingsService::get('session_prices', []),
      'theme_color' => SettingsService::get('theme_color', '#6366F1'),
    ];

    $this->render('app', 'settings/index', [
      'title' => 'Paramètres',
      'settings' => $settings
    ]);
  }

public function update()
{
    try {
        $validated = $this->validateSettings($_POST);
        
        // Debug: Vérifiez les données reçues
        error_log("Données reçues: " . print_r($_POST['session_prices'], true));
        error_log("Données validées: " . print_r($validated['session_prices'], true));

        // Enregistrement
        SettingsService::set('session_prices', $validated['session_prices']);
        SettingsService::set('theme_color', $validated['theme_color']);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Paramètres mis à jour avec succès'
        ];
    } catch (\Exception $e) {
        $_SESSION['flash'] = [
            'type' => 'error', 
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
    
    header('Location: ' . $this->basePath . '/settings');
    exit;
}

protected function validateSettings($data)
{
    $sessionPrices = [];
    
    if (!empty($data['session_prices']) && is_array($data['session_prices'])) {
        foreach ($data['session_prices'] as $entry) {
            if (!empty($entry['type']) && isset($entry['price'])) {
                $type = trim(strip_tags($entry['type']));
                $price = (float) $entry['price'];
                
                if ($type && $price > 0) {
                    $sessionPrices[$type] = $price;
                }
            }
        }
    }

    return [
        'session_prices' => $sessionPrices,
        'theme_color' => $this->validateColor($data['theme_color'] ?? '#6366F1')
    ];
}
  protected function validateColor($color)
  {
    if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
      return $color;
    }
    return '#6366F1'; // Couleur par défaut si invalide
  }
}