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

class IncludeScriptNode extends Node
{
  public static $keyValuePairs = false;
  public static $singleValue = true;
  
  public function compileTag()
  {
    $this->openTag('script', [
      'type' => 'text/javascript',
      'src' => $this->getNode('value'),
      'defer' => 'defer'
    ]);

    $this->closeTag('script');
  }
}
