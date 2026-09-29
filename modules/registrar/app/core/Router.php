<?php
namespace App\Core;

use App\Core\Middleware\MiddlewareRegistry;
use FastRoute;
use FastRoute\RouteCollector;
use App\Controllers\ErrorController;

class Router 
{   

    public function dispatch()
    {       

        $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
            require __DIR__ . '/../../routes/web.php';
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = rtrim(dirname($scriptName), '/');
        $basePath =  strtolower($basePath);

        if (str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
    

        $uri = rtrim($uri, '/') ?: '/';

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        
        $uri = str_replace('\\', '/', $uri);

        $scriptFolder = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

        if (str_starts_with($uri, $scriptFolder)) {
            $uri = substr($uri, strlen($scriptFolder));
        }

        $uri = '/' . trim($uri, '/');
    

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);


        switch ($routeInfo[0]) {

            case FastRoute\Dispatcher::NOT_FOUND:
                http_response_code(404);
                
                $error = new ErrorController();
                $error->notFound();

                break;

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                echo "405 Method Not Allowed";
                break;

           case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                
              if (is_array($handler) && isset($handler['middleware'])) {
                    MiddlewareRegistry::resolve(
                        $handler['middleware']
                    );
                    $handler = $handler['uses'];
                }

                if (is_callable($handler)) {
                    call_user_func_array($handler, $vars);
                    return;
                }
                
                if (is_array($handler)) {
                    [$controller, $method] = $handler;
                    call_user_func_array([new $controller, $method], $vars);
                    return;
                }

                throw new \Exception('Invalid route handler');
                
        }
    }
}
