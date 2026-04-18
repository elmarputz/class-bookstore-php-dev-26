<?php 
declare(strict_types=1);

use Bookshop\Book;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

spl_autoload_register(function ($class) { 
    $filename = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    if (file_exists($filename)) { 
        require_once($filename);
    }
});

Bookshop\SessionContext::create();

$mode = "mock";
// $mnode = "pdo";

require_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'DataManager_' .$mode . ".php");