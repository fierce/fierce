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
define('BASE_PATH', dirname(__DIR__) . '/');

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
  ResponseCache::disable();
  
  print '<pre style="border: 1px solid red; font-family:monospace; display: inline-block; padding: 4px 10px">';
  print htmlspecialchars(print_r($var, true));
  print '</pre>';
  
  if ($exit) {
    exit();
  }
}



// setup autoload
global $autoloadClasses;
$autoloadClasses = array();

$classFiles = glob(BASE_PATH . "fierce/classes/*.php");
foreach ($classFiles as $file) {
  $className = pathinfo($file, PATHINFO_FILENAME);
  $className = strtolower($className);
  $className = preg_replace('/\.class$/', '', $className);
  $className = preg_replace('/[^a-z]/', '', $className);
  $className = 'fierce\\' . $className;
  
  $autoloadClasses[$className] = $file;
}

$classFiles = glob(BASE_PATH . "classes/*.php");
foreach ($classFiles as $file) {
  $className = pathinfo($file, PATHINFO_FILENAME);
  $className = strtolower($className);
  $className = preg_replace('/\.class$/', '', $className);
  $className = preg_replace('/[^a-z]/', '', $className);
  
  $autoloadClasses[$className] = $file;
}

spl_autoload_register(function($className) use ($autoloadClasses) {
  $className = strtolower($className);

  $file = @$autoloadClasses[strtolower($className)];
  if (!$file) {
    return;
  }
  
  require_once $file;
});

// connect to database
$db = new DB('file', BASE_PATH . 'data/');
