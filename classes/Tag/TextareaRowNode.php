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

class TextareaRowNode extends TextareaNode
{
  public static $tagName = 'textarea_row';
  
  public function compileTag()
  {
    $this->openFieldRow('textarea_row');
    
    parent::compileTag();
    
    $this->closeFieldRow();
  }
}
