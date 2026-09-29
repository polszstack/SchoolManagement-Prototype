<?php 

    namespace App\Core;

    use App\Core\Database;
    use Exception;
    use PDO;

    class Model 
    {

        protected PDO $pdo; 
        protected $tableName;
        protected $primaryKey;

        public function __construct(?PDO $pdo = null)
        {
            $this->pdo = $pdo ?? Database::connect();
        }
      
        protected function all()
        {   
            try {

             $stmt = $this->pdo->query("SELECT * FROM {$this->tableName}");
             return $stmt->fetchAll();

            } catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }

       protected function find(int $id)
        {
            try {

                $stmt = $this->pdo->prepare(
                    "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = ?"
                );
                $stmt->execute([$id]);
                return $stmt->fetch() ?: null;

             } catch(Exception $e){
                
                echo $e->getMessage();
            }
        }


        protected function delete(int $id)
        {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        }


        protected function create(array $data)
        {
            $columns = implode(',', array_keys($data));
            $placeholders = implode(',', array_fill(0, count($data), '?'));
            $stmt = $this->pdo->prepare("INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)");
            return $stmt->execute(array_values($data));
        }


        protected function update(int $id, array $data)
        {
            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
            $stmt = $this->pdo->prepare("UPDATE {$this->tableName} SET $set WHERE {$this->primaryKey} = ?");
            return $stmt->execute([...array_values($data), $id]);
        }


         protected function deleteMany(array $ids)
        {
            if (empty($ids)) return false;

            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $stmt = $this->pdo->prepare(
                "DELETE FROM {$this->tableName} 
                WHERE {$this->primaryKey} IN ($placeholders)"
            );

            return $stmt->execute($ids);
        }


     
    }