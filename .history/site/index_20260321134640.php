<?php 

$view = "welcome";

if (isset($_REQUEST["view"])) {
    $view = $_GET["view"];
}


require_once("views/" . $view . ".php");

