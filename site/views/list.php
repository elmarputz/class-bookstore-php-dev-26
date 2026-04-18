<?php 
use Data\DataManager;

$categories = DataManager::getCategories();
$categoryId = isset($_REQUEST['categoryId']) ? (int)$_REQUEST['categoryId'] : null;
$books = $categoryId ? DataManager::getBooksByCategory($categoryId) : null;


require_once("views/partials/header.php"); ?>

<div class="page-header">
    <h2>List of books by category</h2>
</div>

<ul class="nav nav-tabs">
    <?php foreach ($categories as $category): ?>
        <li class="nav-item">
            <a class="nav-link" href="index.php?view=list&categoryId=<?php echo $category->getId(); ?>">
                <?php echo htmlspecialchars($category->getName()); ?>
            </a>
        </li>
    <?php endforeach; ?>

</ul>
<br />
<?php if (isset($books)): ?>
    <?php require_once("views/partials/booklist.php"); ?>
<?php else: ?>
    <div class="alert alert-info">
        <p>Please select a category to view the books.</p>
    </div>
<?php endif; ?>


<?php require_once("views/partials/footer.php"); ?>

