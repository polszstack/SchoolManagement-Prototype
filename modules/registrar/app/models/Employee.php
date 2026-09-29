<?php 


 namespace App\Models;

 use App\Core\Model;

class Employee extends Model 
{

    public $tableName = 'sms_employee';
    public $primaryKey = 'employee_id'; 

    public static function __callStatic($name, $arguments)
    {
                $instance = new self();     
                return $instance->$name(...$arguments);
    }


}