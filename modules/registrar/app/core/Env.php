<?php 

    namespace App\Core;

    class Env {

        public static function load($path){
            
            if(!$path)
            {
                return;
                
            }else{

              $file = $path;
              
              $file_data = file($file,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

               foreach($file_data as $file_datas)
               {        
                    trim($file_datas);

                  if ($file_datas === '' || str_starts_with($file_datas, '#')) {
                        continue;
                    }
                     [$key, $value] = explode('=', $file_datas, 2);
                      $_ENV[$key] = $value;
               }

            }


        }


    }