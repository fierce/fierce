<?php

namespace Fierce;

class CMS
{
  public static function handleRequest($db)
  {
    try {
      $page = $db->page->byId(sha1(Env::get('controller_url')));
    } catch (\Exception $e) {
      $page = $db->page->byId(sha1('/404'));
    }
    
    // display the page
    // ResponseCache::start();
    
    $controllerClass = isset($page->class) ? $page->class : 'Fierce\PageController';
    $controllerClass::run($page);
    
    // ResponseCache::saveCacheIfEnabled();
  }
}
