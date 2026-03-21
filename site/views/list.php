<?php 
use Data\DataManager;

$categories = DataManager::getCategories();
var_dump($categories);

require_once("views/partials/header.php"); ?>

<div class="page-header">
    <h2>List of books by category</h2>
</div>

<?php require_once("views/partials/footer.php"); ?>

