<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Activity extends Model {

    public $tableName = 'rgr_activity_log';
    public $primaryKey = 'id';



    protected function allActivity($paginate = true)
    {
    $perPage = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $offset = ($page - 1) * $perPage;

    $range = isset($_GET['range']) ? $_GET['range'] : 'all';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $where = " WHERE 1=1 ";
    $params = [];

    if (!empty($range) && strtolower($range) !== 'all') {

        [$start, $end]  = $this->rangeDate($range);

        $where .= " AND created_at BETWEEN :start AND :end";
        $params[':start'] = $start;
        $params[':end']   = $end;
       
    }


    if (!empty($search)) {
        $where .= " AND (
            description LIKE :search OR
            action LIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }

  
    $countSql = "SELECT COUNT(*) as total FROM {$this->tableName} $where ORDER BY created_at DESC ";
    $countStmt = $this->pdo->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Base query
    $dataSql = "SELECT * FROM {$this->tableName} $where ORDER BY created_at DESC ";

    if ($paginate) {
        $dataSql .= " LIMIT :limit OFFSET :offset";
    }

    $dataStmt = $this->pdo->prepare($dataSql);

    foreach ($params as $key => $value) {
        $dataStmt->bindValue($key, $value);
    }

    if ($paginate) {
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }

    $dataStmt->execute();
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$paginate) {
        return $data; 
    }

    return [
        'data' => $data,
        'total' => (int) $total,
        'current_page' => $page,
        'last_page' => ceil($total / $perPage)
    ];
}


    public function rangeDate($range)
    {

    switch ($range) {
        
    case 'today':
       $start = date('Y-m-d 00:00:00'); 
        $end   = date('Y-m-d 23:59:59');

        return [$start,$end];

        break;

    case '7days':
        $start = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $end   = date('Y-m-d 23:59:59');
         return [$start,$end];
        break;

    case 'this_week':
        $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $end   = date('Y-m-d H:i:s');
         return [$start,$end];
        break;

    case 'last_week':
        $start = date('Y-m-d 00:00:00', strtotime('monday last week'));
        $end   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
         return [$start,$end];
        break;

    case 'this_month':
        $start = date('Y-m-01 00:00:00');
        $end   = date('Y-m-d H:i:s');
         return [$start,$end];
        break;
    
    case 'last_month':
        $start = date("Y-m-01", strtotime("first day of last month"));
        $end = date("Y-m-t", strtotime("last month"));
         return [$start,$end];
    }

    }


    public static function __callStatic($name, $arguments)
    {
            $instance = new self();     
            return $instance->$name(...$arguments);
    }


    




}