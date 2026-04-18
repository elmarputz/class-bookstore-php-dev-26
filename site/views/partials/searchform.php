<?php 
use Bookshop\Util;
$title = $_REQUEST['title'] ?? null;
?>

<form class="d-flex me-auto" action="/index.php" method="get">
      <input type="hidden" name="view" value="search" />
      <input class="form-control form-control-sm me-2" type="search" id="title" name="title" placeholder="Search book by title..." value="" aria-label="Search">
      <button class="btn btn-outline-success btn-sm" type="submit">Search</button>
</form>