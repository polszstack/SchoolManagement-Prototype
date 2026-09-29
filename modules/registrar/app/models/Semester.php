<?php 

 namespace App\Models;

 use App\Core\Model;
 use Exception;
 use PDO; 


 class Semester extends Model 
 {  

    public $tableName = 'rgr_semesters';
    public $primaryKey = 'id';

    protected function allSemester($paginate = true)
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
        s.name LIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }

  
    $countSql = "SELECT COUNT(*) as total 
             FROM {$this->tableName} s
             LEFT JOIN rgr_school_years sy ON s.school_year_id = sy.id
             $where ";

  
    $countStmt = $this->pdo->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Base query
    $dataSql = "SELECT s.id as id, s.name AS semester,s.is_active as status, sy.name AS school_year
        FROM {$this->tableName} s
        LEFT JOIN rgr_school_years sy
        ON s.school_year_id = sy.id 
        $where ORDER BY sy.name $order";

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


    protected function updateStatus()
    {

         try {

             $this->pdo->query("UPDATE $this->tableName SET is_active = 0");

            } catch(Exception $e)
            {
                echo $e->getMessage();
         }

    }


    public static function __callStatic($name, $arguments)
    {
            $instance = new self();     
            return $instance->$name(...$arguments);
    }



 }