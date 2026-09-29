<?php 

 namespace App\Models;

 use App\Core\Model;

 class Report extends Model 
 {

    public $tableName = 'sd_report_type'; 
    public $primaryKey = 'type_id';

 public static function __callStatic($name, $arguments)
    {
            $instance = new self();     
            return $instance->$name(...$arguments);
    }


 }