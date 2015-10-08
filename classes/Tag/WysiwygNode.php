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
    
    $this->requireScript(\Fierce\Env::get('fierce_src') . 'scripts/wysiwyg.controller.js');
    $this->requireScript(\Fierce\Env::get('fierce_src') . 'third-party/ckeditor/ckeditor.js');
  }
}
