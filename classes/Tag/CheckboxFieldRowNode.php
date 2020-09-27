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

class CheckboxFieldRowNode extends CheckboxFieldNode
{
  public static $tagName = 'checkbox_field_row';
  
  public function compileTag()
  {
    $this->openFieldRow('checkbox_field_row');
    
    parent::compileTag();
    
    $this->closeFieldRow();
  }
}
