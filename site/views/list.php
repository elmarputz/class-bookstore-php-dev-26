<?php 


$book = new Bookshop\Book(1, 1, "The Great Gatsby", "F. Scott Fitzgerald", 10.99);
var_dump($book);

require_once("views/partials/header.php"); ?>

<div class="page-header">
    <h2>List of books by category</h2>
</div>

<?php require_once("views/partials/footer.php"); ?>

