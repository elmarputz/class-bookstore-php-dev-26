<?php 

$view = "welcome";

if (isset($_REQUEST["view"])
    && file_exists(__DIR__ . "views/" . $_REQUEST["view"] . ".php")) {
    $view = $_REQUEST["view"];
}


require_once("views/" . $view . ".php");

