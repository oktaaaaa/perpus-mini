<?php

namespace App\Core;

/**
 * Router mandiri (tanpa framework).
 * Semua request masuk lewat public/index.php lalu di-dispatch ke sini.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, $handler): void
    {
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', trim($path, '/'));
        $pattern = '#^' . $pattern . '$#';

        preg_match_all('#\{([a-zA-Z_]+)\}#', $path, $paramNames);

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'params'  => $paramNames[1],
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        // Hilangkan prefix folder project jika app tidak ada di root domain
        $scriptDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $uri = trim(substr($uri, strlen($scriptDir)), '/');
        }

        $uri = $uri === '' ? '' : $uri;

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $params = array_combine($route['params'], $matches);
                $this->call($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        require BASE_PATH . '/app/Views/errors/404.php';
    }

    private function call($handler, array $params): void
    {
        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();
        call_user_func_array([$controller, $action], $params);
    }
}
