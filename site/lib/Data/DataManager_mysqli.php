<?php

namespace Data;

use Bookshop\Category;
use Bookshop\Book;
use Bookshop\User;
use Bookshop\PagingResult;

/**
 * DataManager — MySQLi Version
 *
 * Uses manual escaping instead of prepared statements.
 * Intentional for teaching SQL injection risks.
 */
class DataManager implements IDataManager {

  private static $__connection;

  /**
   * connect to the database
   *
   * note: alternatively put those in parameter list or as class variables
   *
   * @return \mysqli
   */
  private static function getConnection() {
    if (!isset(self::$__connection)) {

      $type = 'mysql';
      $host = 'db';
      $name = 'db';
      $user = 'db';
      $pass = 'db';

      self::$__connection = new \mysqli($host, $user, $pass, $name);

      if (mysqli_connect_errno()) {
        die('Unable to connect to database.');
      }
    }

    return self::$__connection;
  }

  /**
   * expose the raw mysqli connection for debugging or direct use in demos
   *
   * @return \mysqli
   */
  public static function exposeConnection(): \mysqli {
    return self::getConnection();
  }

  /**
   * place query
   *
   * @return mixed
   */
  private static function query($connection, $query) {
    $res = $connection->query($query);
    if (!$res) {
      die("Error in query \"" . $query . "\": " . $connection->error);
    }

    return $res;
  }

  /**
   * get the key of the last inserted item
   *
   * @return int
   */
  private static function lastInsertId($connection) {
    return mysqli_insert_id($connection);
  }

  /**
   * retrieve an object from the database result set
   *
   * @param object $cursor result set
   *
   * @return object
   */
  private static function fetchObject($cursor) {
    return $cursor->fetch_object();
  }

  /**
   * remove the result set
   *
   * @param object $cursor result set
   *
   * @return null
   */
  private static function close($cursor) {
    return $cursor->close();
  }

  /**
   * close the database connection
   *
   * @param object $cursor resource of current database connection
   *
   * @return null
   */
  private static function closeConnection($connection) {
    $connection->close();
    self::$__connection = null;
  }

  /**
   * get the categories
   *
   * @return array of Category-items
   */
  public static function getCategories(): array {
    $categories = [];
    $con = self::getConnection();
    $res = self::query($con, "
      SELECT id, name
      FROM categories;
      ");
    while ($cat = self::fetchObject($res)) {
      $categories[] = new Category($cat->id, $cat->name);
    }
    self::close($res);
    self::closeConnection($con);
    return $categories;
  }

  /**
   * get the books per category
   *
   * @param int $categoryId numeric id of the category
   *
   * @return array of Book-items
   */
  public static function getBooksByCategory(int $categoryId): array {
    $books      = [];
    $con        = self::getConnection();
    $categoryId = intval($categoryId); // WARNING: manual type cast — no prepared statement
    $res        = self::query($con, "
      SELECT id, categoryId, title, author, price
      FROM books
      WHERE categoryId = " . $categoryId . ";
        ");
    while ($book = self::fetchObject($res)) {
      $books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
    }
    self::close($res);
    self::closeConnection($con);
    return $books;
  }

  /**
   * get the books per search term
   *
   * note: search via LIKE
   *
   * @param string $term search term: book title string match
   *
   * @return array of Book-items
   */
  public static function getBooksForSearchCriteria(string $term): array {
    $books = [];
    $con   = self::getConnection();
    $term  = $con->real_escape_string($term); // WARNING: manual escaping — no prepared statement
    $res   = self::query($con, "
      SELECT id, categoryId, title, author, price
      FROM books
      WHERE title LIKE '%" . $term . "%';
            ");
    while ($book = self::fetchObject($res)) {
      $books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
    }
    self::close($res);
    self::closeConnection($con);
    return $books;
  }

  /**
   * get the books per search term – paginated set only
   *
   * @param string $term       search term: book title string match
   * @param int    $offset     start at the nth item
   * @param int    $numPerPage number of items per page
   *
   * @return PagingResult
   */
  public static function getBooksForSearchCriteriaWithPaging(string $term, int $offset, int $numPerPage): PagingResult {
    $con = self::getConnection();
    //query total count
    $term       = $con->real_escape_string($term); // WARNING: manual escaping — no prepared statement
    $res        = self::query($con, "
      SELECT COUNT(*) AS cnt
      FROM books
      WHERE title LIKE '%" . $term . "%';
        ");
    $totalCount = self::fetchObject($res)->cnt;
    self::close($res);
    //query books to return
    $books      = [];
    $offset     = intval($offset); // WARNING: manual type cast — no prepared statement
    $numPerPage = intval($numPerPage); // WARNING: manual type cast — no prepared statement
    $res        = self::query($con, "
      SELECT id, categoryId, title, author, price
      FROM books
      WHERE title LIKE '%" . $term . "%'
      LIMIT " . $offset . ", " . $numPerPage . ";
        ");
    while ($book = self::fetchObject($res)) {
      $books[] = new Book($book->id, $book->categoryId, $book->title, $book->author, $book->price);
    }
    self::close($res);
    self::closeConnection($con);
    return new PagingResult($books, $offset, $totalCount);
  }

  /**
   * get the User item by id
   *
   * @param int $userId uid of that user
   *
   * @return User|null
   */
  public static function getUserById(int $userId): ?User {
    $user   = null;
    $con    = self::getConnection();
    $userId = intval($userId); // WARNING: manual type cast — no prepared statement
    $res    = self::query($con, "
      SELECT id, userName, passwordHash
      FROM users
      WHERE id = " . $userId . ";
        ");
    if ($u = self::fetchObject($res)) {
      $user = new User($u->id, $u->userName, $u->passwordHash);
    }
    self::close($res);
    self::closeConnection($con);
    return $user;
  }

  /**
   * get the User item by name
   *
   * @param string $userName name of that user - must be exact match
   *
   * @return User|null
   */
  public static function getUserByUserName(string $userName): ?User {
    $user     = null;
    $con      = self::getConnection();
    $userName = $con->real_escape_string($userName); // WARNING: manual escaping — no prepared statement
    $res      = self::query($con, "
      SELECT id, userName, passwordHash
      FROM users
      WHERE userName = '" . $userName . "';
        ");
    if ($u = self::fetchObject($res)) {
      $user = new User($u->id, $u->userName, $u->passwordHash);
    }
    self::close($res);
    self::closeConnection($con);
    return $user;
  }

  /**
   * place to order with the shopping cart items
   *
   * note: wrapped in a transaction
   *
   * @param int    $userId     id of the ordering user
   * @param array  $bookIds    array of book IDs
   * @param string $nameOnCard name on the credit card
   * @param string $cardNumber credit card number
   *
   * @return int
   */
  public static function createOrder(int $userId, array $bookIds, string $nameOnCard, string $cardNumber): int {
    $con = self::getConnection();
    self::query($con, 'BEGIN;');
    $userId     = intval($userId); // WARNING: manual type cast — no prepared statement
    $nameOnCard = $con->real_escape_string($nameOnCard); // WARNING: manual escaping — no prepared statement
    $cardNumber = $con->real_escape_string($cardNumber); // WARNING: manual escaping — no prepared statement
    self::query($con, "
      INSERT INTO orders (
        userId
        , creditCardNumber
        , creditCardHolder
      ) VALUES (
        " . $userId . "
        , '" . $cardNumber . "'
        , '" . $nameOnCard . "'
      );
      ");
    $orderId = self::lastInsertId($con);
    $orderId = intval($orderId); // WARNING: manual type cast — no prepared statement
    foreach ($bookIds as $bookId) {
      $bookId = intval($bookId); // WARNING: manual type cast — no prepared statement
      self::query($con, "
        INSERT INTO orderedbooks (
          orderId,
          bookId
        ) VALUES (
          " . $orderId . "
          , " . $bookId . ");
      ");
    }
    self::query($con, 'COMMIT;');
    self::closeConnection($con);
    return $orderId;
  }
}
