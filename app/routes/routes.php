<?php

use FastRoute\RouteCollector;
use App\Controllers\AuthController;
use App\Controllers\FileController;
use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\CustomerController;
use App\Controllers\MaterialController;
use App\Controllers\DashboardController;
use App\Controllers\ProductionStepController;

$basePath = '/Projets/autres/hiernostine/public';

// Définir les routes publiques
$publicRoutes = [
  $basePath . '/',
  $basePath . '/login',
  $basePath . '/register',
  $basePath . '/forgot-password',
  $basePath . '/reset-password',
  $basePath . '/reset-password/{token}'
];

// Appliquer le middleware
$authMiddleware = new \App\Middleware\AuthMiddleware($sessionManager, $basePath);
$authMiddleware->handle($publicRoutes);

// Configuration des routes
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) use ($basePath) {
  $r->addRoute('GET', $basePath . '/customer', [CustomerController::class, 'index']);
  $r->addRoute('GET', $basePath . '/customer/create', [CustomerController::class, 'create']);
  $r->addRoute('POST', $basePath . '/customer/store', [CustomerController::class, 'store']);
  $r->addRoute('POST', $basePath . '/customer/addForSession', [CustomerController::class, 'addForSession']);
  $r->addRoute('GET', $basePath . '/customer/export', [CustomerController::class, 'export']);
  $r->addRoute('GET', $basePath . '/customer/edit/{id:\d+}', [CustomerController::class, 'edit']);
  $r->addRoute('POST', $basePath . '/customer/update/{id:\d+}', [CustomerController::class, 'update']);
  $r->addRoute('POST', $basePath . '/customer/delete/{id:\d+}', [CustomerController::class, 'delete']);

  $r->addRoute('GET', $basePath . '/order', [OrderController::class, 'index']);
  $r->addRoute('GET', $basePath . '/order/create', [OrderController::class, 'create']);
  $r->addRoute('POST', $basePath . '/order/store', [OrderController::class, 'store']);
  $r->addRoute('POST', $basePath . '/order/addForSession', [OrderController::class, 'addForSession']);
  $r->addRoute('GET', $basePath . '/order/export', [OrderController::class, 'export']);
  $r->addRoute('GET', $basePath . '/order/edit/{id:\d+}', [OrderController::class, 'edit']);
  $r->addRoute('POST', $basePath . '/order/update/{id:\d+}', [OrderController::class, 'update']);
  $r->addRoute('POST', $basePath . '/order/delete/{id:\d+}', [OrderController::class, 'delete']);
  $r->addRoute('GET', $basePath . '/order/{id:\d+}/production', [OrderController::class, 'productionTracking']);
  $r->addRoute('GET', $basePath . '/order/show/{id:\d+}', [OrderController::class, 'show']);

  // Routes pour les fichiers
  $r->addRoute('GET', $basePath . '/file/list/{order_id:\d+}', [FileController::class, 'listFiles']);
  $r->addRoute('GET', $basePath . '/file/upload/{order_id:\d+}', [FileController::class, 'showUploadForm']);
  $r->addRoute('POST', $basePath . '/file/upload/{order_id:\d+}', [FileController::class, 'upload']);
  $r->addRoute('GET', $basePath . '/file/download/{id:\d+}', [FileController::class, 'download']);
  $r->addRoute('POST', $basePath . '/file/delete/{id:\d+}', [FileController::class, 'delete']);

  // Étapes de production
  $r->addRoute('GET', $basePath . '/production/create/{order_id:\d+}', [ProductionStepController::class, 'create']);
  $r->addRoute('POST', $basePath . '/production/store/{order_id:\d+}', [ProductionStepController::class, 'store']);
  $r->addRoute('GET', $basePath . '/production/edit/{order_id:\d+}/{id:\d+}', [ProductionStepController::class, 'edit']);
  $r->addRoute('POST', $basePath . '/production/update/{order_id:\d+}/{id:\d+}', [ProductionStepController::class, 'updateStatus']);
  $r->addRoute('POST', $basePath . '/production/delete/{id:\d+}', [ProductionStepController::class, 'delete']);


  // Matériaux
  $r->addRoute('GET', $basePath . '/material', [MaterialController::class, 'index']);
  $r->addRoute('GET', $basePath . '/material/create', [MaterialController::class, 'create']);
  $r->addRoute('POST', $basePath . '/material/store', [MaterialController::class, 'store']);
  $r->addRoute('GET', $basePath . '/material/edit/{id:\d+}', [MaterialController::class, 'edit']);
  $r->addRoute('POST', $basePath . '/material/update/{id:\d+}', [MaterialController::class, 'update']);
  $r->addRoute('POST', $basePath . '/material/delete/{id:\d+}', [MaterialController::class, 'delete']);

  $r->addRoute('GET', $basePath . '/', [AuthController::class, 'showLoginForm']);
  $r->addRoute('GET', $basePath . '/login', [AuthController::class, 'showLoginForm']);
  $r->addRoute('POST', $basePath . '/login', [AuthController::class, 'login']);
  $r->addRoute('GET', $basePath . '/register', [AuthController::class, 'showRegisterForm']);
  $r->addRoute('POST', $basePath . '/register', [AuthController::class, 'register']);
  $r->addRoute('GET', $basePath . '/profile', [UserController::class, 'showProfile']);
  $r->addRoute('POST', $basePath . '/profile/update-info', [UserController::class, 'updateProfileInfo']);
  $r->addRoute('POST', $basePath . '/profile/update-password', [UserController::class, 'updateProfilePassword']);
  $r->addRoute('POST', $basePath . '/profile/delete', [UserController::class, 'deleteProfile']);
  $r->addRoute('GET', $basePath . '/logout', [AuthController::class, 'logout']);
  $r->addRoute('GET', $basePath . '/forgot-password', [AuthController::class, 'showForgotPasswordForm']);
  $r->addRoute('POST', $basePath . '/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
  $r->addRoute('GET', $basePath . '/reset-password/{token}', [AuthController::class, 'showResetForm']);
  $r->addRoute('POST', $basePath . '/reset-password', [AuthController::class, 'resetPassword']);

  // Routes pour la gestion des utilisateurs (admin)
  $r->addRoute('GET', $basePath . '/user', [UserController::class, 'index']);
  $r->addRoute('GET', $basePath . '/user/create', [UserController::class, 'create']);
  $r->addRoute('POST', $basePath . '/user/store', [UserController::class, 'store']);
  $r->addRoute('GET', $basePath . '/user/edit/{id:\d+}', [UserController::class, 'edit']);
  $r->addRoute('POST', $basePath . '/user/update/{id:\d+}', [UserController::class, 'update']);
  $r->addRoute('POST', $basePath . '/user/delete/{id:\d+}', [UserController::class, 'delete']);

  $r->addRoute('GET', $basePath . '/dashboard', [DashboardController::class, 'index']);

});