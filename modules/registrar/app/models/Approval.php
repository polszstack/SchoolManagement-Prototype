<?php 

  namespace App\Models;

  use App\Core\Model;
  use PDO;

class Approval extends Model 
{

    public $tableName = 'sd_approvals';
    public $primaryKey = 'approval_id';


    protected function allApproval($paginate = true)
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
            title LIKE :search OR
            decision LIKE :search
        )";
        $params[':search'] = "%{$search}%";
    }

  
    $countSql = "SELECT COUNT(*) as total FROM {$this->tableName} $where ORDER BY approved_at $order ";
    $countStmt = $this->pdo->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Base query
   $dataSql = "SELECT 
            s.*,
            CONCAT(se.first_name,' ',se.last_name) AS submitter,
            CONCAT(ae.first_name,' ',ae.last_name) AS approver
        FROM {$this->tableName} s
        LEFT JOIN sms_employee se ON s.submit_by = se.employee_id
        LEFT JOIN sms_employee ae ON s.approver_id = ae.employee_id
        $where
        ORDER BY s.approved_at $order";

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