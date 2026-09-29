<?php 
class DamageController 
{

    public function index()
    {
        require __DIR__ . '/../../config/database.php';
        
        
     try {
            $db = Database::connect();

            $stmt = $db->prepare("SELECT * FROM lab_damage");
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            require __DIR__ . '/../views/damages/damage.php';
           

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

    }

    public function create()
    {
        require __DIR__ . '/../../config/database.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $item = $_POST['item'];
                $description = $_POST['description'];
                $status = $_POST['status'];
                $code = $_POST['code'];
    
                try {
                    $db = Database::connect();
                    $stmt = $db->prepare("INSERT INTO lab_damage (category, code,description, status) VALUES (:category, :code, :description, :status)");
                    $stmt->bindParam(':category', $item);
                    $stmt->bindParam(':code', $code);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':status', $status);
                    $stmt->execute();
    
                    header('Location: ' . BASE_URL . '/damages');
                    exit();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }

    }

    public function view($id)
    {

         header('Content-Type: application/json');

        require __DIR__ . '/../../config/database.php';

        try {
            $db = Database::connect();
            $stmt = $db->prepare("SELECT * FROM lab_damage WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $damage = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($damage) {
                echo json_encode($damage);
            } else {
                echo json_encode(['error' => 'Damage not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {

        header('Content-Type: application/json');
        require __DIR__ . '/../../config/database.php';

         try {
            $db = Database::connect();
            $stmt = $db->prepare("SELECT * FROM lab_damage WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $damage = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($damage) {
                echo json_encode($damage);
            } else {
                echo json_encode(['error' => 'Damage not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

    }

    
    public function update() {
    header('Content-Type: application/json');

    require __DIR__ . '/../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


       $id = $_POST['id'] ?? null;
        $item = $_POST['edit_item'] ?? '';
        $description = $_POST['edit_description'] ?? '';
        $status = $_POST['edit_status'] ?? '';
        $code = $_POST['edit_code'] ?? ''; 

        try {
            $db = Database::connect();

            $stmt = $db->prepare("
                UPDATE lab_damage
                SET category = :category,
                    code = :code,
                    description = :description,
                    status = :status
                WHERE id = :id
            ");

            $stmt->execute([
                ':category' => $item,
                ':code' => $code,
                ':description' => $description,
                ':status' => $status,
                ':id' => $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Damage updated successfully']);

        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

    public function destroy($id)
    {

        header('Content-Type: application/json');

         require __DIR__ . '/../../config/database.php';
        
    
        try {
            $db = Database::connect();

            $stmt = $db->prepare("DELETE FROM lab_damage WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode([
                'success' => true,
                'message' => 'Damage deleted successfully'
            ]);

        } catch(PDOException $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }


    }




}

