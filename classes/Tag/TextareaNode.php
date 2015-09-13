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

class TextareaNode extends FieldNode
{
  public static $tagName = 'textarea';
  
  public function compileTag()
  {
    $attributes = [
      'name' => $this->getNode('name'),
      'id' => $this->idNode(),
    ];
    if ($this->hasNode('class')) {
      $attributes['class'] = $this->getNode('class');
    }

    $this->openTag('textarea', $attributes);
    $this->text($this->valueNode());
    $this->closeTag('textarea');
  }
}
