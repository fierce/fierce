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

class FormNode extends \Twig_Node
{
  public static $currentForm = null;
  
  public function compile(\Twig_Compiler $compiler)
  {
    if (self::$currentForm) {
      throw new exception('cannot nest forms');
    }
    self::$currentForm = $this;
    
    $compiler
      ->write("print '<form method=\"post\"';\n")
    ;
    
    if ($this->hasNode('action')) {
      $compiler
        ->write("print ' action=\"' . ")
        ->subcompile($this->getNode('action'))
        ->raw(" . '\"';\n")
      ;
    }
    
    if ($this->hasNode('class')) {
      $compiler
        ->write("print ' class=\"' . ")
        ->subcompile($this->getNode('class'))
        ->raw(" . '\"';\n")
      ;
    }
    
    $compiler
      ->write("print \">\\n\";\n")
    ;

        
    $compiler
      ->subcompile($this->getNode('body'))
    ;
    
    $compiler
      ->write('
        print "</form>\n";
      ')
    ;
    
    self::$currentForm = null;
  }
}
