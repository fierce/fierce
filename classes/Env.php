<?php

namespace Fierce;

class Env
{
  public static $vars = [];
  protected static $varPriorities = [];
  
  public static function set($name, $value, $priority = 0)
  {
    self::init();
    
    if (isset(self::$vars[$name]) && self::$varPriorities[$name] > $priority) {
      return;
    }
    
    self::$vars[$name] = $value;
    self::$varPriorities[$name] = $priority;
    
    if ($name == 'base_url') {
      self::initItemsBasedOnBaseUrl();
    }
  }
  
  public static function get($name)
  {
    self::init();
    
    if (!isset(self::$vars[$name])) {
      throw new \Exception('Undefined Env var: "' . $name . '"');
      return null;
    }
    
    return self::$vars[$name];
  }
  
  public static function init()
  {
    static $first = true;
    if (!$first) {
      return;
    }
    $first = false;
    
    // default env vars
    Env::set('fierce_path', dirname(__DIR__) . '/', -10);
    Env::set('base_path', dirname(dirname(dirname(dirname(__DIR__)))) . '/', -10);
    
    Env::set('fierce_src', str_replace(Env::get('base_path'), '', Env::get('fierce_path')), -10);
    
    if (isset($_SERVER['REQUEST_URI'])) {
      Env::set('base_url', 'http://' . $_SERVER['SERVER_NAME'] . '/', -10);
    } else {
      Env::set('base_url', false, -10);
    }
  }
  
  public static function initItemsBasedOnBaseUrl()
  {
    if (isset($_SERVER['REQUEST_URI'])) {
      $requestUri = $_SERVER['REQUEST_URI'];
      if (preg_match('#https?://[^/]+(/.*)/#', Env::get('base_url'), $matches)) {
        $baseUrlBaseUri = $matches[1];
        if (substr($requestUri, 0, strlen($baseUrlBaseUri)) == $baseUrlBaseUri) {
          $requestUri = substr($requestUri, strlen($baseUrlBaseUri));
        }
      }
      
      Env::set('request_url', $requestUri, -10);
      
    } else {
      Env::set('request_url', false, -10);
    }
    
    $url = parse_url(Env::get('request_url'), PHP_URL_PATH);
    if ($url != '/') {
      $url = rtrim($url, '/');
    }
    Env::set('controller_url', $url, -10);
    Env::set('page_class', $url == '/'? 'home' : preg_replace('/[^a-z0-9]+/', '-', ltrim(strtolower($url), '/')) . '-page', -10);
  }
}
