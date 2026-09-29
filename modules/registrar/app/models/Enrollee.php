<?php 

 namespace App\Models;
 use App\Core\Model;

 use PDO;

 class Enrollee extends Model
 {
     public $tableName = 'enr_applicants';
    public $primaryKey = 'id'; 


    protected function allEnrollee($paginate = true)
    {
    $perPage = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $offset = ($page - 1) * $perPage;

    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $where = " WHERE 1=1 ";
    $params = [];

    if (!empty($status) && strtolower($status) !== 'all') {
        $where .= " AND academic_status = :status";
        $params[':status'] = $status;
    }

    if (!empty($search)) {
        $where .= " AND (
            student_number LIKE :search OR
            first_name LIKE :search OR
            last_name LIKE :search OR
            course LIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }


    // school year

    $schoolYearSql = "SELECT name as school_year FROM rgr_school_years WHERE is_active = true";
    $schoolYearStmt = $this->pdo->prepare($schoolYearSql);

    $schoolYearStmt->execute();
    $school_year = $schoolYearStmt->fetch(PDO::FETCH_ASSOC)['school_year'];



    // Count total
    $countSql = "SELECT COUNT(*) as total FROM {$this->tableName} $where";
    $countStmt = $this->pdo->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Base query
    $dataSql = "SELECT * FROM {$this->tableName} $where";

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
        // return $data; // For PDF

        return [
            'data' => $data,
            'schoolYear' => $school_year
        ];

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