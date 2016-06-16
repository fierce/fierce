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
  
  public $identifier = 'main';
  
  public function __construct($line, $tag = null, $identifier = 'main')
  {
    parent::__construct([], [], $line, $tag);
    
    $this->identifier = $identifier;
  }

  public function compile(\Twig_Compiler $compiler)
  {
    $compiler
      ->addDebugInfo($this)
      ->write('
        \Fierce\Tag\NavNode::printNav("' . preg_replace('[^a-zA-Z0-9-_]', '', $this->identifier) . '");
      ')
    ;
  }
  
  static public function printNav($identifier='main')
  {
    // fetch all pages, sorted by position
    $pages = \Fierce\Page::all('nav_position');    
    
    print '<ul class="nav">';
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
      
      $isCurrent = \Fierce\Env::get('request_url') == $navPage->url;
      
      $classHtml = $isCurrent ? ' class="current"' : '';
      
      print '<li' . $classHtml . '><a href="' . $href . '">' . $name . '</a>';
      
      $first = false;
    }
    
    // close off open tags
    while ($depth >= 0) {
      print '</li></ul>';
      $depth--;
    }
  }
}
