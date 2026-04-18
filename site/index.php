<?php 

require_once("inc/bootstrap.php");

$view = "welcome";

if (isset($_REQUEST["view"])
    && file_exists(__DIR__ . "/views/" . $_REQUEST["view"] . ".php")) {
    $view = $_REQUEST["view"];
}

$postAction = $_REQUEST[Bookshop\Controller::ACTION] ?? null;

if ($postAction !== null) {
    try {
            Bookshop\Controller::getInstance()->invokePostAction();
    }
    catch (Exception $e) {  
        // Log the error, show an error message, etc.
        // For simplicity, we'll just ignore the error and continue.
    }
}


require_once("views/" . $view . ".php");

