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
        \Fierce\Tag\NavNode::printNav();
      ')
    ;
  }
  
  static public function printNav($identifier='main')
  {
    // fetch all pages, sorted by position
    $pages = \Fierce\Page::all('nav_position');    
    
    print '<ul id="nav">';
    $depth = 0;
    $first = true;
    foreach ($pages as $navPage){
      // is this page in the nav?
      if (@$navPage->nav != $identifier) {
        continue;
      }
      
      // no change to depth? close the list item we left open in the last iteration.
      if (!$first && $navPage->nav_position_depth == $depth) {
        print '</li>';
      }
      
      // increase depth?
      while ($navPage->nav_position_depth > $depth) {
        print '<ul>';
        $depth++;
      }
      
      // reduce depth?
      if ($navPage->nav_position_depth < $depth){
        while ($navPage->nav_position_depth < $depth) {
          print '</li></ul>';
          $depth--;
        }
        print '</li>';
      }
      
      // generate a nav item, leaving the list item open.
      $href = ltrim($navPage->url, '/');
      if ($href == '') {
        $href = './';
      }
      $href = htmlspecialchars($href);
      
      $name = htmlspecialchars($navPage->name);
      
      print '<li><a href="' . $href . '">' . $name . '</a>';
      
      $first = false;
    }
    
    // close off open tags
    while ($depth >= 0) {
      print '</li></ul>';
      $depth--;
    }
  }
}
