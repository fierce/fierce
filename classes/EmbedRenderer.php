<?php

namespace Fierce;

class EmbedRenderer
{
  private $db;
  
  public function __construct()
  {
    global $db;
    
    $this->db = $db;
  }
  
  public function page($url)
  {
    $id = sha1($url);
    if (!$this->db->page->idExists($id)) {
      print '<!-- page not found: "' . htmlspecialchars($url) . '" -->';
      return;
    }
    
    $page = $this->db->page->byId($id);
    
    $class = preg_replace('/[^a-z0-9]+/', '_', strtolower('embed_' . $url));
    
    print '<div class="' . $class . '">';
    View::renderString($page->content);
    print '</div>';
  }
}
