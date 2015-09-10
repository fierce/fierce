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

class WysiwygNode extends Node
{
  public static $tagName = 'wysiwyg';
  
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
      'id' => $idNode,
      'class' => 'wysiwyg'
    ];

    $this->openTag('textarea', $attributes);
    
    $this->text($valueNode);
    
    $this->closeTag('textarea');
    
    \Fierce\View::addScript(FIERCE_SRC . 'scripts/wysiwyg.controller.js');
    \Fierce\View::addScript(FIERCE_SRC . 'third-party/ckeditor/ckeditor.js');
  }
}
