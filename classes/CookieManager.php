<?php

namespace Fierce;

class CookieManager
{
  public function get($key, $default=null)
  {
    if (!isset($_COOKIE[$key])) {
      return $default;
    }
    
    return $_COOKIE[$key];
  }
  
  public function set($key, $value, $timeout = null)
  {
    $expire = $timeout == null ? 0 : time() + $timeout;
     
    setcookie($key, $value, $expire, '/');
    
    $_COOKIE[$key] = $value;
  }
  
  public function clear($key)
  {
    setcookie($key, '', time() - 3600, '/');
    
    unset($_COOKIE[$key]);
  }
}
