<?php

/**
 * =============================================================
 *  GERBANG TUNGGAL APLIKASI (Single Entry Point)
 *  Semua request HTTP wajib lewat file ini (lihat public/.htaccess)
 * =============================================================
 */

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/app/Core/autoload.php';
require dirname(__DIR__) . '/app/Core/helpers.php';

use App\Core\Router;

$router = new Router();
require dirname(__DIR__) . '/config/routes.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
