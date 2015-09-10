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
    $this->openTag('div', [
      'class' => 'row wysiwyg_row'
    ]);
    
    $this->openTag('label', [], false);
    
    if ($this->hasNode('label')) {
      $this->text($this->getNode('label'));
    } else {
      $this->compiler
        ->write('$fieldName = ')
        ->subcompile($this->getNode('name'))
        ->raw(";\n")
      ;
      $this->compiler
        ->write('$displayName = ucwords(str_replace(\'_\', \' \', ')
        ->subcompile($this->getNode('name'))
        ->raw("));\n")
      ;
      
      $this->compiler
        ->write("print htmlspecialchars(\$displayName);\n")
      ;
    }
    
    $this->closeTag('label');
    
    parent::compileTag();
    
    $this->closeTag('div');
  }
}
