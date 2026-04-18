<?php
namespace Bookshop;

class Controller {

    public const string ACTION_ADD = 'add';
    public const string ACTION_REMOVE = 'remove';

    public const string ACTION = 'action';
    public const string PAGE = 'page';

    private static ?Controller $instance = null;

    public static function getInstance() : Controller {
        if (self::$instance === null) {
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function invokePostAction() : never {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
           throw new \Exception('Invalid request method');
        }
        elseif (!isset($_POST[self::ACTION])) {
            throw new \Exception('Missing action parameter');
        }

        $action = $_POST[self::ACTION];
        switch ($action) {
            case self::ACTION_ADD:
                ShoppingCart::add((int)$_POST['bookId']);
                Util:redirect();
                break;
            case self::ACTION_REMOVE:
                ShoppingCart::remove((int)$_POST['bookId']);
                Util:redirect();
                break;
            default:
                throw new \Exception('Unknown action: ' . $action);
        }
        
    }



}