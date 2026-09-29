<?php   
    
    namespace App\Core;
    
    use Exception;
    use PDO;
    use PDOException;

    class Database {
        
        protected static ?PDO $pdo = null;
        protected static array $config; 

        public static function init(array $config)
        {
            self::$config = $config;
        }

        public static function connect()
        {   

            if(self::$pdo != null)
            {
                return self::$pdo;
            }

            try {

            self::$pdo = new PDO(self::$config['driver'].":host=".self::$config['host'].";dbname=".self::$config['db'].";charset=utf8mb4",
                            self::$config['user'], self::$config['pass'], [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);

             return self::$pdo;      

            }catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            } catch (Exception $e) {
                echo "General error: " . $e->getMessage();
            }   
            
        }



    }
