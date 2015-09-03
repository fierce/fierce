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
  public static $tagName = 'field';
  
  public function compileTag()
  {
    $idNode = new \Twig_Node_Expression_Binary_Concat(
      $this->getNode('name'),
      new \Twig_Node_Expression_Constant('_field', $this->lineno),
      $this->lineno
    );
    
    $valueNode = new \Twig_Node_Expression_GetAttr(
      new \Twig_Node_Expression_Name('fierceCurrentFormData', $this->lineno),
      $this->getNode('name'),
      null,
      'any',
      $this->lineno
    );
    
    $attributes = [
      'name' => $this->getNode('name'),
      'type' => $this->hasNode('type') ? $this->getNode('type') : 'text',
      'id' => $idNode,
      'value' => $valueNode
    ];

    $this->openTag('input', $attributes);
  }
}
