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

class FieldRowNode extends FieldNode
{
  public static $tagName = 'field_row';
  
  public function compileTag()
  {
    $this->openFieldRow();
    
    parent::compileTag();
    
    $this->closeFieldRow();
  }
}
