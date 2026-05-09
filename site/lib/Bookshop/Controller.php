<?php
namespace Bookshop;

class Controller {

    public const string ACTION_ADD = 'add';
    public const string ACTION_REMOVE = 'remove';

    public const string ACTION = 'action';
    public const string PAGE = 'page';
    public const string USER_NAME = 'userName';
    public const string USER_PASSWORD = 'password';
    public const string CC_NAME = 'nameOnCard';
    public const string CC_NUMBER = 'cardNumber';

    public const string ACTION_ORDER = 'placeOrder';


    public const string ACTION_LOGIN = 'login';
    public const string ACTION_LOGOUT = 'logout';



    private static ?Controller $instance = null;

    public static function getInstance() : Controller {
        if (self::$instance === null) {
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function invokePostAction() : never {
           

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
           throw new \Exception('Invalid request method');
        }
        elseif (!isset($_REQUEST[self::ACTION])) {
            throw new \Exception('Missing action parameter');
        }

        
    
        $action = $_REQUEST[self::ACTION];
   

        switch ($action) {
            case self::ACTION_ADD:
           
                ShoppingCart::add((int)$_REQUEST['bookId']);
                Util::redirect();
            
                break;
            case self::ACTION_REMOVE:
                ShoppingCart::remove((int)$_REQUEST['bookId']);
                Util::redirect();
                break;

            case self::ACTION_LOGIN:
            
                if (!AuthenticationManager::authenticate($_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD])) {
                   $this->forwardRequest(array('invalid credentials'));
                }
                Util::redirect();
                break;

            case self::ACTION_LOGOUT:
                AuthenticationManager::signOut();
                Util::redirect();
                break;


            case self::ACTION_ORDER: 
                $user = AuthenticationManager::getAuthenticatedUser();
                if ($user == null) {
                    $this->forwardRequest(['Not logged in']);
                    break;
                }
                if (!$this->processCheckout($_REQUEST[self::CC_NAME], $_REQUEST[self::CC_NUMBER])) {
                    $this->forwardRequest(['checkout failed']);
                }
                break;


            default:
                throw new \Exception('Unknown action: ' . $action);
        }
        
    }

  protected function forwardRequest(?array $errors = null, ?string $target = null): never {
    // check for given target and try to fall back to previous page if needed
    if ($target == null) {
      if (!isset($_REQUEST[self::PAGE])) {
        throw new \Exception('Missing target for forward.');
      }
      $target = $_REQUEST[self::PAGE];
    }

    // optional - add errors to redirect and process them in view
    if (count($errors) > 0) {
      $_SESSION['errors'] = $errors;
    }

    // forward request to target
    header('location: ' . $target);
    exit();
  }


  /**
   * @param ?string $nameOnCard name as it appears on the credit card
   * @param ?string $cardNumber 16-digit credit card number
   * @return bool
   */
  // ?string — explicit nullable type (PHP 8.1+ deprecates `string $x = null`)
  protected function processCheckout(?string $nameOnCard = null, ?string $cardNumber = null): bool {
    $errors = [];
    $nameOnCard = trim($nameOnCard);
    if ($nameOnCard == null || strlen($nameOnCard) == 0) {
      $errors[] = 'Invalid name on card.';
    }
    if ($cardNumber == null || strlen($cardNumber) != 16 || !ctype_digit($cardNumber)) {
      $errors[] = 'Invalid card number. Card number must be sixteen digits.';
    }

    if (sizeof($errors) > 0) {
      $this->forwardRequest($errors);
      return false;
    }

    // check cart
    if (ShoppingCart::size() == 0) {
      $this->forwardRequest(['Shopping cart is empty.']);
      return false;
    }

    // try to place a new order
    $user = AuthenticationManager::getAuthenticatedUser();
    $orderId = \Data\DataManager::createOrder($user->getId(), ShoppingCart::getAll(), $nameOnCard, $cardNumber);
    if (!$orderId) {
      $this->forwardRequest(['Could not create order.']);
      return false;
    }
    // clear shopping cart and redirect to success page
    ShoppingCart::clear();
    Util::redirect('index.php?view=success&orderId=' . rawurlencode($orderId));

    return true;
  }




}