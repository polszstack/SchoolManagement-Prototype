<?php 

    namespace App\Controllers;
    use App\Core\Controller; 
    use App\Helper\Links; 
  

    class ErrorController extends Controller
    {

        public function notFound(){

    
          $urlToBase  = Links::goTo('base');
          $redirectToHome  = Links::goTo('/');
  
          return $this->render('errors/error',
          [
                  'base_url' => $urlToBase,
                  'home' => $redirectToHome
                ]);

        }


    }