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
    
    if ($this->hasNode('value')) {
      $attributes['value'] = $this->getNode('value');
    } else if (FormNode::$currentForm && FormNode::$currentForm->hasNode('data')) {
      $dataNode = FormNode::$currentForm->getNode('data');
      
      // TODO: compile $dataNode into something like:
      // print twig_escape_filter($context['loginData']->username);
    }
    
    $this->openTag('input', $attributes);
  }
}
