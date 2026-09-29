<?php 

    namespace App\Helper;


    class Response {

       public static function redirect(string $uri, int $status = 302)
        {
            http_response_code($status);
            $base = BASE_URL;
            header("Location: $base$uri");
            exit;
        }

        public static function json(array $data, int $status = 200)
        {
            http_response_code($status);
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        }



    }