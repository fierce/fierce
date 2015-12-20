<?php

namespace Fierce;

class CMS
{
  public static function handleRequest($db)
  {
    $pages = $db->page->find(['url' => Env::get('controller_url')]);

    if (count($pages) == 0) {
      HTTP::notFoundHeader();
      $pages = $db->page->find(['url' => '/404']);
    }
    
    if (count($pages) == 0) {
      die('Page not found');
    }
    
    $page = array_shift($pages);
    
    // display the page
    // ResponseCache::start();
    
    $controllerClass = $page->class;
    $controllerClass::run($page, $db);
    
    // ResponseCache::saveCacheIfEnabled();
  }
}
