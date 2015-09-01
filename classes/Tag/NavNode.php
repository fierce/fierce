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

namespace Fierce\Tag;

class NavNode extends \Twig_Node
{
  public function __construct($line, $tag = null)
  {
    parent::__construct([], [], $line, $tag);
  }

  public function compile(\Twig_Compiler $compiler)
  {
    $compiler
      ->addDebugInfo($this)
      ->write('
        global $db;
        
        $navPages = Fierce\Page::all(\'nav_position\');
        
        print "<ul id=\\"nav\\">\n";
        foreach ($navPages as $navPage) {
          if (!@$navPage->nav_position) {
            continue;
          }
          
          $name = htmlspecialchars($navPage->name);
          
          $href = ltrim($navPage->url, \'/\');
          if ($href == \'\') {
            $href = \'./\';
          }
          $href = htmlspecialchars($href);
          
          $classHtml = $navPage->url == REQUEST_URL ? \' class="current"\' : \'\';
          
          print "<li{$classHtml}><a href=\"$href\">$name</a></li>\n";
        }
        print "</ul>\n";
      ')
    ;
  }
}
