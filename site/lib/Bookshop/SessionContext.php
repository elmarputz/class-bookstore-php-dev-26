<?php
namespace Bookshop;

class SessionContext {

    private static $exists = false;

    public static function create() {
        if (!self::$exists) {
            session_start();
            self::$exists = true;
        }
        return self::$exists;
    }

}