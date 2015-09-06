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
  public static $tagName = 'nav';
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
        $lastRight = 0;
        $navDepth = 0;
        foreach ($navPages as $navPage) {
          if (@$navPage->nav != \'main\') {
            continue;
          }
          
          if ($navPage->nav_position > $lastRight) {
            foreach (range($lastRight + 1, $navPage->nav_position) as $index) {
              if ($index != $lastRight + 1) {
                print "</ul>\n";
                print "</li>\n";
                $navDepth--;
              }
            }
          }
          
          $name = htmlspecialchars($navPage->name);
          
          $href = ltrim($navPage->url, \'/\');
          if ($href == \'\') {
            $href = \'./\';
          }
          $href = htmlspecialchars($href);
          
          $classHtml = $navPage->url == REQUEST_URL ? \' class="current"\' : \'\';
          
          print "<li{$classHtml}><a href=\"$href\">$name</a>";
          
          if ($navPage->nav_position_right - $navPage->nav_position > 1) {
            print "<ul>\n";
            $navDepth++;
          } else {
            print "</li>\n";
          }
          
          $lastRight = $navPage->nav_position_right;
        }
        while ($navDepth >= 0) {
          print "</ul>\n";
          $navDepth--;
        }
      ')
    ;
    
//     dp($compiler->getSource());
  }
}
