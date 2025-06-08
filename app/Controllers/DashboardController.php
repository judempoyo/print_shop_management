<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Photo;
use App\Models\Customer;
use App\Models\Material;
use App\Core\ViewRenderer;
use App\Models\PhotoSession;
use App\Models\ProductionStep;
use App\Services\SessionManager;


class DashboardController
{
    use ViewRenderer;
    protected $session;
    protected $basePath;

  
    protected $sessionPrices = [];

    public function __construct()
    {
        $this->session = new SessionManager();
        $this->session->start();
        $this->basePath = '/Projets/autres/hiernostine/public';
    }

public function index()
{
    $stats = [
        'total_customers' => Customer::count(),
        'customer_growth' => $this->calculateGrowth(Customer::class),
        'active_orders' => Order::whereNotIn('status', ['delivered', 'canceled'])->count(),
        'orders_this_week' => Order::where('created_at', '>=', date('Y-m-d', strtotime('-1 week')))->count(),
        'in_production' => Order::whereIn('status', ['in_printing', 'in_finishing'])->count(),
        'production_progress' => $this->calculateProductionProgress(),
        'to_deliver' => Order::where('status', 'ready_for_delivery')->count(),
        'delivered_today' => Order::where('status', 'delivered')
                                ->whereDate('updated_at', date('Y-m-d'))
                                ->count(),
    ];

    $recent_orders = Order::with('customer')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

    $low_stock_materials = Material::whereColumn('stock_quantity', '<=', 'min_stock_level')
                                ->orderBy('stock_quantity')
                                ->limit(5)
                                ->get();

  
    $recent_activity = $this->getRecentActivity();

    $this->render('app', 'dashboard', [
        'stats' => $stats,
        'recent_orders' => $recent_orders,
        'low_stock_materials' => $low_stock_materials,
        'recent_activity' => $recent_activity,
        'title' => 'Tableau de bord'
    ]);
}

protected function calculateGrowth($model)
{
    $currentMonthCount = $model::whereYear('created_at', date('Y'))
                             ->whereMonth('created_at', date('m'))
                             ->count();
    
    $lastMonthCount = $model::whereYear('created_at', date('Y', strtotime('last month')))
                          ->whereMonth('created_at', date('m', strtotime('last month')))
                          ->count();
    
    if ($lastMonthCount == 0) return 100;
    
    return round(($currentMonthCount - $lastMonthCount) / $lastMonthCount * 100);
}

protected function calculateProductionProgress()
{
    $totalSteps = ProductionStep::count();
    if ($totalSteps == 0) return 0;
    
    $completedSteps = ProductionStep::where('status', 'completed')->count();
    
    return round(($completedSteps / $totalSteps) * 100);
}

protected function getRecentActivity()
{
    return [
        [
            'type' => 'order',
            'title' => 'Nouvelle commande',
            'description' => 'Commande #ORD-2023-001 créée',
            'time' => 'Il y a 2 heures'
        ],
        [
            'type' => 'file',
            'title' => 'Fichier uploadé',
            'description' => 'Bon de commande.pdf ajouté',
            'time' => 'Il y a 4 heures'
        ],
       
    ];
}

}