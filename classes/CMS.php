<?php

namespace Fierce;

class CMS
{
  public static function handleRequest($db)
  {
    $id = sha1(Env::get('controller_url'));
    
    if ($db->page->idExists($id)) {
      $page = $db->page->byId($id);
    } else {
      $page = $db->page->byId(sha1('/404'));
    }
    
    // display the page
    // ResponseCache::start();
    
    $controllerClass = isset($page->class) ? $page->class : 'Fierce\PageController';
    $controllerClass::run($page, $db);
    
    // ResponseCache::saveCacheIfEnabled();
  }
}