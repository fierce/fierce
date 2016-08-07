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
    
    if (!headers_sent()) {
      header('Location: ' . $url);
    } else {
      print '<meta http-equiv="refresh" content="0; url=';
      print htmlspecialchars($url);
      print '">';

      print '<p>Please continue to <a href="';
      print htmlspecialchars($url);
      print '">';
      print htmlspecialchars($url);
      print '</a></p>';
    }
    exit;
  }
  
  static public function notFoundHeader()
  {
    if (headers_sent()) {
      throw new \Exception('Attempt to send not found header when it has already been sent');
    }
    header('HTTP/1.0 404 Not Found');
  }
}
