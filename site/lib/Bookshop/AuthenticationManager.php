<?php 

namespace Bookshop;

use Data\DataManager;

SessionContext::create();

class AuthenticationManager {

    public static function authenticate(string $userName, string $password) : bool {
       
        $user = DataManager::getUserByUserName($userName); 
        if ($user != null && $user->getPasswordHash() === hash('sha1', $userName  . '|' . $password)) {
            $_SESSION['user'] = $user->getId();
            return true;
        }
        return false;

    }

    public static function signOut() : void {
        unset($_SESSION['user']);
    }

    public static function isAuthenticated() : bool {
        return isset($_SESSION['user']);
    }

    public static function getAuthenticatedUser() : ?User {
        if (self::isAuthenticated()) {
            $userId = $_SESSION['user'];
            return DataManager::getUserById($userId);
        }
        return null;
    }

}