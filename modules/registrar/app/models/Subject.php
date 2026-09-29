<?php 

namespace App\Models;

use App\Core\Model;
use PDO;


class Subject extends Model
{

     public $tableName = 'rgr_subjects';
     public $primaryKey = 'id';


     protected function allSubject($paginate=true)
     {

        $perPage = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $offset = ($page - 1) * $perPage;

        $order = isset($_GET['order']) ? $_GET['order'] : 'desc';

        $order = in_array($order, ['asc', 'desc']) 
                ? strtoupper($order) 
                : 'DESC';

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $where = " WHERE 1=1 ";
        $params = [];


    if (!empty($search)) {
        $where .= " AND (
            name LIKE :search OR
            code LIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }

  
    $countSql = "SELECT COUNT(*) as total FROM {$this->tableName} $where ORDER BY name $order ";
    $countStmt = $this->pdo->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Base query
    $dataSql = "SELECT * FROM {$this->tableName} $where ORDER BY name $order";

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
     


    public static function __callStatic($name, $arguments)
    {
            $instance = new self();     
            return $instance->$name(...$arguments);
    }


}

