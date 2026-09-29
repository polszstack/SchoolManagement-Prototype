<?php 




class HeInvController {



    public function index()
    {

    
        require __DIR__ . '/../../config/database.php';
        
        
        try {
                $db = Database::connect();

                $stmt = $db->prepare("SELECT * FROM lab_inventory");
                $stmt->execute();

                $inventories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                require __DIR__ . '/../views/inventories/home-eco/he-inventory.php';

            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

    }


    public function view($id)
    {

         header('Content-Type: application/json');

        require __DIR__ . '/../../config/database.php';

        try {
            $db = Database::connect();
            $stmt = $db->prepare("SELECT * FROM lab_inventory WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $inventory = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($inventory) {
                echo json_encode($inventory);
            } else {
                echo json_encode(['error' => 'Damage not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

    }

    public function create()
    {

          require __DIR__ . '/../../config/database.php';



        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

          $imagePath = null;

         $uploadDir = __DIR__ . '/../../public/uploads/';

            if(!is_dir($uploadDir)){
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $fileName;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)){
                $imagePath = 'uploads/' . $fileName;
            }
        }


      
        $category = $_POST['category'] ?? '';
        $total = $_POST['total'] ?? '';
        $available =$_POST['available'] ?? '';


         try {

            $db = Database::connect();

            $stmt = $db->prepare("
                INSERT INTO lab_inventory(category, total, available, item_img)
                VALUES(:category, :total, :available, :image)
            ");

            $stmt->execute([
                ':category' => $category,
                ':total' => $total,
                ':available' => $available,
                ':image' => $imagePath
            ]);

             header('Location: ' . BASE_URL . '/he-inventory');
             exit();
            
        } catch(PDOException $e) {

            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);

        }


    }

    public function update()
    {

    header('Content-Type: application/json');

    require __DIR__ . '/../../config/database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


        $id = $_POST['id'] ?? null;
        $category = $_POST['edit_category'] ?? '';
        $available = $_POST['edit_available'] ?? '';
        $total = $_POST['edit_total'] ?? '';

        try {
            $db = Database::connect();

            $stmt = $db->prepare("
                    UPDATE lab_inventory 
                    SET category = :category,
                        total = :total,
                        available = :available
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':category' => $category,
                    ':total' => $total,
                    ':available' => $available,
                    ':id' => $id,
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

            $stmt = $db->prepare("DELETE FROM lab_inventory WHERE id = :id");
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