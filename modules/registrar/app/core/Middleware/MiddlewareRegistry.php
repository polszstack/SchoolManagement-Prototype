<?php 

namespace App\Core\Middleware;

class MiddlewareRegistry
{
    protected static array $map = [
        'auth' => AuthMiddleware::class,
        // 'guest' => GuestMiddleware::class,
    ];

    public static function resolve(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (!isset(self::$map[$middleware])) {
                throw new \Exception("Middleware [$middleware] not registered");
            }

            (new self::$map[$middleware])->handle();
        }
    }
}
