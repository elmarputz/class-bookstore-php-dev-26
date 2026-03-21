<?php 

$view = "welcome";

if (isset($_GET["view"])) {
    $view = $_GET["view"];
}


require_once("views/" . $view . ".php");

