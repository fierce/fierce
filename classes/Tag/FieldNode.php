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
  
  public function idNode()
  {
    if ($this->hasNode('id')) {
      return $this->getNode('id');
    }
    
    $this->compiler
      ->write('$context[\'_fierce_field_id\'] = trim(preg_replace(\'/[^a-zA-Z0-9_]+/\', \'_\', ')
      ->subcompile($this->getNode('name'))
      ->raw("), '_') . '_field';\n")
    ;
    
    return new \Twig_Node_Expression_Name('_fierce_field_id', $this->lineno);
  }
  
  public function valueNode()
  {
    if ($this->hasNode('value')) {
      return $this->getNode('value');
    }
    
    return new \Twig_Node_Expression_GetAttr(
      new \Twig_Node_Expression_Name('_fierce_current_form_data', $this->lineno),
      $this->getNode('name'),
      null,
      'any',
      $this->lineno
    );
  }
  
  public function compileTag()
  {
    $attributes = [
      'name' => $this->getNode('name'),
      'type' => $this->hasNode('type') ? $this->getNode('type') : 'text',
      'id' => $this->idNode(),
      'value' => $this->valueNode()
    ];
    
    if ($this->hasNode('class')) {
      $attributes['class'] = $this->getNode('class');
    }
    
    if ($this->hasNode('placeholder')) {
      $attributes['placeholder'] = $this->getNode('placeholder');
    }

    $this->openTag('input', $attributes);
  }
  
  public function openFieldRow($extraClass=false)
  {
    $this->openTag('div', [
      'class' => 'row' . ($extraClass ? " $extraClass" : '')
    ]);
    
    $this->openTag('label', [], false);
    
    if ($this->hasNode('label')) {
      $this->text($this->getNode('label'));
    } else {
      $this->compiler
        ->write('$fieldName = ')
        ->subcompile($this->getNode('name'))
        ->raw(";\n")
      ;
      $this->compiler
        ->write('$displayName = ucwords(str_replace(\'_\', \' \', ')
        ->subcompile($this->getNode('name'))
        ->raw("));\n")
      ;
      
      $this->compiler
        ->write("print htmlspecialchars(\$displayName);\n")
      ;
    }
    
    $this->closeTag('label');
  }
  
  public function closeFieldRow()
  {
    $this->closeTag('div');
  }
}
