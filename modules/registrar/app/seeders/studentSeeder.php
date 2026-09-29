<?php 

 namespace App\Seeders;

 use Faker\Factory;
use OverflowException;

 class StudentSeeder 
 {  

    public $numberOfSample;

    public function __construct($numberOfSample) {

        $this->numberOfSample = $numberOfSample;

    }

    public function run()
    {   

        try {

        $faker = Factory::create();

        $data = [];

        for ($i = 0; $i < $this->numberOfSample; $i++) {
            $data[] = [
                'name' => $faker->name,
                'emailss' => $faker->email
            ];

            $this->progressBar($i, $this->numberOfSample);
        }

        file_put_contents('users.json', json_encode($data, JSON_PRETTY_PRINT));

         echo "Student Seeding Completed Ma-men.";

        } catch(OverflowException $e)
        {
            echo "error ma-men: ".$e->getMessage() . PHP_EOL;

        }catch (\Exception $e) {
            echo "error ma-men: ".$e->getMessage() . PHP_EOL;
        }

    }

    private function progressBar($done, $total, $size = 20)
    {
        $percent = ($done + 1 ) / $total;

        $filled = floor($percent * $size);

        $bar = str_repeat("=", $filled) . str_repeat(" ", $size - $filled);

        $percentText = round($percent * 100);

        echo "\r[$bar] $percentText%". PHP_EOL;
    }

 }

