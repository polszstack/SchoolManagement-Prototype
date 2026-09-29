<?php 

    namespace App\Models;
    use App\Core\Model;


    class User extends Model
    {
        
        public $tableName = 'user_tbl';
        public $primaryKey = 'user_id';


          public static function __callStatic($name, $arguments)
        {
            $instance = new self();     
            return $instance->$name(...$arguments);
        }
      
      

    }