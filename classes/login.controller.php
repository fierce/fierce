<?

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class LoginController extends PageController
{
  public function __construct()
  {
    ResponseCache::disable();
    parent::__construct();
  }
  public function defaultAction()
  {
    $loginData = (object)[
      'return' => @$_GET['return']
    ];
    $message = false;
    
    $this->display('login.tpl', get_defined_vars());
  }
  
  public function submitAction()
  {
    $loginData = (object)array_merge($_POST, $_GET);
    
    $success = Auth::attemptLogin(@$loginData->email, @$loginData->password);
    
    if (!$success) {
      $message = 'Incorrect login details, please try again.';
    } else {
      HTTP::redirect(@$loginData->return);
    }
    
    $this->display('login.tpl', get_defined_vars());
  }
  
  public function logoutAction()
  {
    Auth::logout();
    
    $this->display('logout.tpl', get_defined_vars());
  }
}
