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

class WysiwygNode extends FieldNode
{
  public static $tagName = 'wysiwyg';
  
  public function compileTag()
  {
    $attributes = [
      'name' => $this->getNode('name'),
      'id' => $this->idNode(),
      'class' => 'wysiwyg'
    ];

    $this->openTag('textarea', $attributes);
    $this->text($this->valueNode());
    $this->closeTag('textarea');
    
    $this->requireScript(FIERCE_SRC . 'scripts/wysiwyg.controller.js');
    $this->requireScript(FIERCE_SRC . 'third-party/ckeditor/ckeditor.js');
  }
}
