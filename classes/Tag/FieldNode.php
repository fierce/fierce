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

class FieldNode extends Node
{
  public function compileTag()
  {
    $attributes = [
      'type' => $this->hasNode('type') ? $this->getNode('type') : 'text',
      'name' => $this->getNode('name')
    ];
    
   
    
    // compile tag
    $this->compiler
      ->write("print '<input';\n")
    ;
    foreach ($attributes as $name => $value) {
      $this->compiler
        ->write("print ' $name=\"'")
      ;
      if (is_string($value)) {
        $this->compiler
          ->raw(" . \"" . addslashes(htmlspecialchars($value)) . "\"")
        ;
      } else if (is_a($value, 'Twig_Node')) {
        $this->compiler
          ->raw(' . ')
          ->subcompile($value)
        ;
      } else {
        throw new \exception("invalid value " . print_r($value, true));
      }
      $this->compiler
        ->raw(" . '\"';\n")
      ;
    }
    
    if ($this->hasNode('value')) {
      $this->compiler
        ->write("print ' $name=\"'")
      ;
      $this->compiler
        ->raw(' . ')
        ->subcompile($this->getNode('value'))
      ;
      $this->compiler
        ->raw(" . '\"';\n")
      ;
    } else if (FormNode::$currentForm && FormNode::$currentForm->hasNode('data')) {
      $dataNode = FormNode::$currentForm->getNode('data');
      
      
      // TODO: compile $dataNode into something like:
      
      
      
      // print twig_escape_filter($context['loginData']->username);
    }
    
    $this->compiler
      ->write("print \">\\n\";\n")
    ;
    
//     dp($this->compiler->getSource());
  }
}
