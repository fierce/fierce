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

// config
define('FIERCE_PATH', __DIR__ . '/');
define('BASE_PATH', dirname(dirname(dirname(__DIR__))) . '/');
define('FIERCE_SRC', str_replace(BASE_PATH, '', FIERCE_PATH));

preg_match('#^(/[^/]+)(.*)#', $_SERVER['REQUEST_URI'], $matches);
define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . $matches[1] . '/');
define('REQUEST_URL', $matches[2]);

require BASE_PATH . 'fierce-config.php';

if (!defined('F_AUTH_SALT')) {
  throw new exception('Auth salt must be defined');
}
if (F_AUTH_SALT == '8d6f6390017eb415bcf468a050d893628e40d12f') {
  throw new exception('Cannot use the example F_AUTH_SALT. Make your own with `random | sha1`!');
}

// function to print out a variable for debugging
function dp($var, $exit=true)
{
  if (class_exists('Fierce\\ResponseCache')) {
    Fierce\ResponseCache::disable();
  }
  
  print '<pre style="border: 1px solid red; font-family:monospace; display: inline-block; padding: 4px 10px">';
  print htmlspecialchars(print_r($var, true));
  print '</pre>';
  
  if ($exit) {
    exit();
  }
}

// connect to database
$db = new Fierce\DB('file', BASE_PATH . 'data/');
