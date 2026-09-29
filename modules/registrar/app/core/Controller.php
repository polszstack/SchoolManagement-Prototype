<?php 

    namespace App\Core;
    
    use Twig\Environment;
    use Twig\Error\Error;


    class Controller
    {
       

        public function render(string $uri,array $data = [])
        {

            extract($data);
            require __DIR__ . '/../views/' . $uri . '.php';
        }


    }