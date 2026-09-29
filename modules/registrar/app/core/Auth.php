<?php

namespace App\Core;
use App\Core\Database;
use PDO;

class Auth {

    public static function attempt($email, $password) {

        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        Session::set('user_id', $user['id']);
        return true;
    }

    public static function check() {
        return Session::get('user_id') !== null;
    }

    public static function logout() {
        Session::destroy();
    }
}
