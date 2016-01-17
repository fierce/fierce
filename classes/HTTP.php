<?php

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

namespace Fierce;

class HTTP
{
  static public function redirect($url)
  {
    if (!preg_match('#^https?://#', $url)) {
      $url = Env::get('base_url') . ltrim($url, '/');
    }
    ResponseCache::disable();
    
    header('Location: ' . $url);
    exit;
  }
  
  static public function notFoundHeader()
  {
    header('HTTP/1.0 404 Not Found');
  }
}
