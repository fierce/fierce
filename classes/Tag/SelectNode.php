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

class SelectNode extends FieldNode
{
  public static $tagName = 'select_field';
  
  public function compileTag()
  {
    $this->openTag('select', [
      'id' => $this->idNode(),
      'name' => $this->getNode('name')
    ]);
    
    $this->compiler
        ->addDebugInfo($this)
        // the (array) cast bypasses a PHP 5.2.6 bug
        ->write("\$context['_fierce_select_options'] = twig_ensure_traversable(")
        ->subcompile($this->getNode('options'))
        ->raw(");\n")
    ;
    $this->compiler
      ->write("\$fierceSelectUseNameAsValue = array_keys(\$context['_fierce_select_options']) === range(0, count(\$context['_fierce_select_options']) - 1);\n")
      ->write("foreach (\$context['_fierce_select_options'] as \$fierceSelectOptionValue => \$fierceSelectOptionName) {\n")
      ->indent()
      ->write("\$valueHtml = \$fierceSelectUseNameAsValue ? '' : 'value=\"' . htmlspecialchars(\$fierceSelectOptionValue) . '\"';\n")
      ->write("\$value = \$fierceSelectUseNameAsValue ? \$fierceSelectOptionName : \$fierceSelectOptionValue;\n")
      ->write("\$selectedHtml = \$value == ")
      ->subcompile($this->valueNode())
      ->raw(" ? 'selected' : '';\n")
      ->write("print \"<option \$valueHtml \$selectedHtml>\" . htmlspecialchars(\$fierceSelectOptionName) . \"</option>\";\n")
      ->outdent()
      ->write("}\n");
    ;
    
    $this->closeTag('select');
    
    // add js
    $this->requireScript(\Fierce\Env::get('fierce_src') . 'scripts/tag-field.controller.js');
  }
}
