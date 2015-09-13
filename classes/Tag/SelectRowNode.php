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

class SelectRowNode extends SelectNode
{
  public static $tagName = 'select_row';
  
  public function compileTag()
  {
    $this->openFieldRow('select_row');
    
    parent::compileTag();
    
    $this->closeFieldRow();
  }
}
