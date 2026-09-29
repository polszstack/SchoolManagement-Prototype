<?php 

    namespace App\Core\Middleware;

    use App\Core\Auth;
    use App\Core\Response;

    class AuthMiddleware implements MiddlewareInterface
    {

        public function handle()
        {
            if(!Auth::check())
            {
                Response::redirect('/');
            }

        }


    }