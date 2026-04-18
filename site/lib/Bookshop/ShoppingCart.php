<?php 
namespace Bookshop;
SessionContext::create();

class ShoppingCart {

  public static function add(int $bookId) {
    $cart = self::getCart();
    $cart[$bookId] = $bookId;
    self::storeCart($cart);
  }

    public static function remove(int $bookId) {
        $cart = self::getCart();
        unset($cart[$bookId]);
        self::storeCart($cart);
    }


    public static function contains(int $bookId) : bool {
        $cart = self::getCart();
        return array_key_exists($bookId, $cart);
    }

  private static function getCart() : array {
    return $_SESSION['cart'] ?? array();
  }

  private static function storeCart(array $cart) : void {
    $_SESSION['cart'] = $cart;
  }
}