<?php

namespace Fierce;

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
  public $mainTpl = 'main-admin.tpl';
  
  public function __construct()
  {
    ResponseCache::disable();
    
    if (!$this->displayName) {
      $this->displayName = 'Login Form';
    }
    
    parent::__construct();
  }
  public function defaultAction()
  {
    $loginData = new FormData([
      'email',
      'password',
      'return'
    ]);
    $loginData->retrieve();
    
    $message = false;
    
    $pageTitle = 'Log In';
    $this->display('login.tpl', get_defined_vars());
  }
  
  public function submitAction()
  {
    $loginData = new FormData([
      'email',
      'password',
      'return'
    ]);
    $loginData->retrieve();
    
    $success = Auth::attemptLogin(@$loginData->email, @$loginData->password);
    
    if (!$success) {
      $message = 'Incorrect login details, please try again.';
    } else {
      HTTP::redirect(@$loginData->return);
    }
    
    $pageTitle = 'Log In';
    $this->display('login.tpl', get_defined_vars());
  }
  
  public function logoutAction()
  {
    Auth::logout();
    
    $pageTitle = 'Logout';
    $this->display('logout.tpl', get_defined_vars());
  }
}
