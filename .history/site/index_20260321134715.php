<?php 

$view = "welcome";

if (isset($_REQUEST["view"])
    && file_exists("views/" . $_REQUEST["view"] . ".php")) {
    $view = $_GET["view"];
}


require_once("views/" . $view . ".php");

