<?php 
    
    require_once __DIR__ . '/../bootstrap.php';

    use App\Seeders\StudentSeeder;
    
    try {

     $seeder = new StudentSeeder(10);
     $seeder->run();
     
    }catch(OverflowException $e){

        echo "error ma-men: ".$e->getMessage() . PHP_EOL;

    }catch (\Exception $e) {
        echo "error ma-men: ".$e->getMessage() . PHP_EOL;
    }

   

   