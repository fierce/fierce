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

/**
 * This file initializes the Fierce environment.
 */

namespace Fierce;

// config
define('FIERCE_PATH', __DIR__ . '/');
define('BASE_PATH', dirname(dirname(dirname(__DIR__))) . '/');
define('FIERCE_SRC', str_replace(BASE_PATH, '', FIERCE_PATH));

preg_match('#^(/[^/]+)(.*)#', $_SERVER['REQUEST_URI'], $matches);
define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . $matches[1] . '/');
define('REQUEST_URL', $matches[2]);

require BASE_PATH . 'fierce-config.php';

if (!defined('F_AUTH_SALT')) {
  throw new \exception('Auth salt must be defined');
}
if (F_AUTH_SALT == '8d6f6390017eb415bcf468a050d893628e40d12f') {
  throw new \exception('Cannot use the example F_AUTH_SALT. Make your own with `random | sha1`!');
}

// function to print out a variable for debugging
function dp($var, $exit=true)
{
  if (class_exists('Fierce\\ResponseCache')) {
    ResponseCache::disable();
  }
  
  print '<pre style="border: 1px solid red; font-family:monospace; display: inline-block; padding: 4px 10px">';
  print htmlspecialchars(print_r($var, true));
  print '</pre>';
  
  if ($exit) {
    exit();
  }
}



// setup autoload
spl_autoload_register(function($className) {
  // no dots or slashes allowed in a class name (could be code injection attack)
  if (strpos($className, '.') !== false || strpos($className, '/') !== false) {
    return;
  }
  
  // find namespace
  $hasNamespace = preg_match('/^([^\\\\]+)\\\\(.*)/', $className, $namespaceMatches);
  
  if (!$hasNamespace) {
    $fileName = str_replace('\\', '/', $className);
    require_once BASE_PATH . 'classes/' . $className . '.php';
    return;
  }
  
  if ($namespaceMatches[1] != 'Fierce') {
    return;
  }
  
  $fileName = str_replace('\\', '/', $namespaceMatches[2]) . '.php';
  require_once FIERCE_PATH . 'classes/' . $fileName;
});

// connect to database
$db = new DB('file', BASE_PATH . 'data/');
