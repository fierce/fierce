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

class DateFieldRowNode extends DateFieldNode
{
  public static $tagName = 'date_field_row';
  
  public function compileTag()
  {
    $this->openFieldRow('date_field_row');
    
    parent::compileTag();
    
    $this->closeFieldRow();
  }
}
