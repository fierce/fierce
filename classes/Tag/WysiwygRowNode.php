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

class WysiwygRowNode extends WysiwygNode
{
  public static $tagName = 'wysiwyg_row';
  
  public function compileTag()
  {
    $this->openFieldRow('wysiwyg_row');
    
    parent::compileTag();
    
    $this->closeFieldRow();
  }
}
