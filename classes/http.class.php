<?

namespace F;

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class HTTP
{
  public static function redirect($url)
  {
    $url = BASE_URL . ltrim($url, '/');
    ResponseCache::disable();
    
    header('Location: ' . $url);
    exit;
  }
}
