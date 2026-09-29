<?php 

namespace App\Helper;
use App\Core\Database;


class Logger
{
    public static function log($action, $description = null)
    {
        $pdo = Database::connect(); 

        $userId = $_SESSION['user']['id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt = $pdo->prepare("
            INSERT INTO rgr_activity_log (user_id, action, description, ip_address)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$userId, $action, $description, $ip]);
    }
}