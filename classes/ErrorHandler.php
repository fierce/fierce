<?php

namespace Fierce;

class ErrorHandler
{
  public static $customHandler = null;
  
  public static function register()
  {
    set_error_handler([get_called_class(), 'handleError']);
  }
  
  public static function handleError($errno, $errstr, $errfile, $errline, $errcontext)
  {
    $errorNames = [
      E_ERROR => 'E_ERROR',
      E_WARNING => 'E_WARNING',
      E_PARSE => 'E_PARSE',
      E_NOTICE => 'E_NOTICE',
      E_CORE_ERROR => 'E_CORE_ERROR',
      E_CORE_WARNING => 'E_CORE_WARNING',
      E_COMPILE_ERROR => 'E_COMPILE_ERROR',
      E_COMPILE_WARNING => 'E_COMPILE_WARNING',
      E_USER_ERROR => 'E_USER_ERROR',
      E_USER_WARNING => 'E_USER_WARNING',
      E_USER_NOTICE => 'E_USER_NOTICE',
      E_STRICT => 'E_STRICT',
      E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
      E_DEPRECATED => 'E_DEPRECATED',
      E_USER_DEPRECATED => 'E_USER_DEPRECATED',
    ];
    $errorName = isset($errorNames[$errno]) ? $errorNames[$errno] : "Error $errno";
    
    if (error_reporting() == 0) { // @ operator cancelled errors
      return;
    }
    
    if (self::$customHandler) {
      $closure = self::$customHandler;
      $closure($errno, $errstr, $errfile, $errline, $errcontext, $errorName);
    }
    
    print '<pre style="border: 3px solid red; font-family:monospace; display: inline-block; padding: 4px 10px">';
    print htmlspecialchars("$errorName $errstr\n\n$errfile on line $errline");
    print '</pre>';
    
    exit();
  }
}
