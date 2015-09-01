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
  * This file takes an incoming request, searches the database for a page entry,
  * creates a controller and executes it – hopefully capturing the response to
  * be written to a static html file.
  */

namespace Fierce;

// init composer
$autoloader = require dirname(dirname(__DIR__)) . '/autoload.php';

// init fierce
require __DIR__ . '/init.php';

// setup autoloader for global scope
$autoloader->addPsr4(false, BASE_PATH . 'classes/');

// find the page
$url = parse_url(REQUEST_URL, PHP_URL_PATH);
if ($url != '/') {
  $url = rtrim($url, '/');
}
define('CONTROLLER_URL', $url);
unset($url);

try {
  $page = $db->page->byId(sha1(CONTROLLER_URL));
} catch (\Exception $e) {
  $page = $db->page->byId(sha1('/404'));
}

// display the page
ResponseCache::start();

$controllerClass = $page->class;
$controllerClass::run($page);

ResponseCache::saveCacheIfEnabled();
