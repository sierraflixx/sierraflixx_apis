<?php

class Router
{
    public $request;
    public $routes = [];

    private $base;

    public function getBase(): string
    {
        return $this->base;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function __construct(array $request)
    {
        $env = ($_SERVER['SERVER_NAME'] === 'localhost' ? "DEVELOPMENT" : "PRODUCTION") . "_PATH";
        $this->base = $_ENV[$env];

        $this->request = str_replace($this->base, "", $request['REQUEST_URI']);
    }

    public function addRoute(string $url, \Closure$fn): void
    {
        $this->routes[$url] = $fn;
    }

    public function hasRoute(string $uri): bool
    {
        return array_key_exists($uri, $this->routes);
    }

    public function run()
    {
        if ($this->hasRoute($this->request)) {
            $this->routes[$this->request]->call($this);
        } else {
            $this->routes['404']->call($this);
        }
    }
}
