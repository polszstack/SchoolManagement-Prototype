<?php 

 namespace App\Models;

 use App\Core\Model;

class Department extends Model 
{

    public $tableName = 'sd_department';
    public $primaryKey = 'department_id';

    

     public static function __callStatic($name, $arguments)
    {
                $instance = new self();     
                return $instance->$name(...$arguments);
    }


}