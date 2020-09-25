<?php
namespace app;

class Router
{
    private $routes = [];

    public function addRouter(array $router): bool
    {
        if (!isset($router['action']) || !isset($router['url']) || !isset($router['method'])) {
            return false;
        }
        $this->routes[] = $router;
        return true;
    }

    public function existRoute(string $requestUri, string $requestMethod): ?array
    {
        $existRoute = [];
        foreach ($this->routes as $route) {
            if ($route['method'] == $requestMethod && preg_match('/^'.preg_quote($route['url'], '/').'$/', $requestUri)) {
                $existRoute = $route;
                break;
            }
        }
        return !empty($existRoute) ? $existRoute : null;
    }
}