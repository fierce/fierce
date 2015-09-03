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

class IncludeCssNode extends Node
{
  public static $tagName = 'include_css';
  public static $keyValuePairs = false;
  public static $singleValue = true;

  
  public function compileTag()
  {
    $this->openTag('style', ['type' => 'text/css']);
    
    
    $this->text([
      '/* ',
      $this->getNode('value'),
      " */\n"
    ]);
    
    $this->compiler
      ->write("print file_get_contents(BASE_PATH . ")
      ->subcompile($this->getNode('value'))
      ->write(");\n")
    ;
    
    $this->closeTag('style');
  }
}
