<?php

    namespace App\Helper;

    class Links {

        public static function goTo($uri)
        {   

            if($uri === '/' || $uri === 'base'){
                return BASE_URL;
            }else{
                return BASE_URL.$uri;
            }

        }


    }