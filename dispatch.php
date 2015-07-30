<?

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */ 

require './init.php';

// find the page
$url = parse_url(REQUEST_URL, PHP_URL_PATH);
if ($url != '/') {
  $url = rtrim($url, '/');
}

try {
  $page = $db->page->byId(sha1($url));
} catch (Exception $e) {
  $page = $db->page->byId(sha1('/404'));
}

// display the page
ResponseCache::start();

$controllerClass = $page->class;
$controllerClass::run($page);

ResponseCache::saveCacheIfEnabled();