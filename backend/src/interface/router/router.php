<?php
declare(strict_types=1);

namespace app\interface\router;

require_once(__DIR__ . "/../../infrastructure/http/respond.php");

use app\infrastructure\http\Respond;

class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes["GET"][$path] = $handler;
    }
    public function post(string $path, callable $handler): void
    {
        $this->routes["POST"][$path] = $handler;
    }

    public function execute(string $method, string $uri): void
    {
        $method = strtoupper($method);

        if(!isset($this->routes[$method]))
            Respond::json(["error" => "Invalid request method"], 405);

        $markPos = strpos($uri, "?");
        if(is_int($markPos))
            $uri = substr($uri, 0, $markPos);

        foreach($this->routes[$method] as $route => $handler)
        {
            $routePattern = preg_replace("#\{[A-Za-z0-9]+\}#", "([A-Z0-9]+)", $route);
            $routePattern = "#^.+/" . $routePattern . "$#";

            if(!preg_match($routePattern, $uri, $matches))
                continue;
            
            if(count($matches) > 1)
                $matches = [$matches[1]];

            $handler(...$matches);
        }
        
        Respond::json(["error" => "Endpoint: $uri not found"], 404);
    }
}