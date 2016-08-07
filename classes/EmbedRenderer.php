<?php

namespace Fierce;

class EmbedRenderer
{
  private $db;
  
  public function __construct()
  {
    $this->db = Env::get('db');;
  }
  
  public function page($url)
  {
    $pages = $this->db->page->find(['url' => $url]);
    
    if (count($pages) == 0) {
      print '<!-- page not found: "' . htmlspecialchars($url) . '" -->';
      return;
    }
    
    $page = array_shift($pages);
    $controllerClass = $page->class;
    
    $class = preg_replace('/[^a-z0-9]+/', '_', strtolower('embed_' . $url));
    print '<div class="' . $class . '"><div class="' . $class .'_inner">';
    
    $controllerClass::run($page, $this->db, ['content_only' => true]);
    
    print '</div></div>';
  }
}
