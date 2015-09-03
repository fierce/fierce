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
  public static $tagName = 'form';
  
  public function compile(\Twig_Compiler $compiler)
  {
    $compiler
      
    ;
    if ($this->hasNode('data')) {
      $compiler
        ->write("\$context['fierceCurrentFormData'] = ")
        ->subcompile($this->getNode('data'))
        ->raw(";\n");
      ;
    } else {
      $compiler
        ->write("\$context['fierceCurrentFormData'] = null;\n")
      ;
    }
    
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
    $compiler
      ->write("unset(\$context['fierceCurrentFormData']);\n");
    ;
  }
}
