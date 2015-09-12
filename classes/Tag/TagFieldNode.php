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

class TagFieldNode extends FieldNode
{
  public static $tagName = 'tag_field';
  
  public function compileTag()
  {
    // apply class to the field
    $classNode = new \Twig_Node_Expression_Constant('tag_field large ', $this->lineno);
    
    if ($this->hasNode('class')) {
      $classNode = new \Twig_Node([
        $classNode,
        $this->getNode('class')
      ]);
    }
    $this->setNode('class', $classNode);

    
    
    // create list id node
    $this->compiler
      ->write('$context[\'_fierce_tag_list_id\'] = trim(preg_replace(\'/[^a-zA-Z0-9_]+/\', \'_\', ')
      ->subcompile($this->getNode('name'))
      ->raw("), '_') . '_tags';\n")
    ;
    $listIdNode = new \Twig_Node_Expression_Name('_fierce_tag_list_id', $this->lineno);
    
    
    // output field
    parent::compileTag();
    
    // output list
    $this->openTag('ul', [
      'id' => $listIdNode,
      'class' => 'tag_list'
    ]);
    
    $this->compiler
        ->addDebugInfo($this)
        // the (array) cast bypasses a PHP 5.2.6 bug
        ->write("\$context['_fierce_tag_list_options'] = twig_ensure_traversable(")
        ->subcompile($this->getNode('options'))
        ->raw(");\n")
    ;
    $this->compiler
      ->write("foreach (\$context['_fierce_tag_list_options'] as \$fierceTagListOption) {\n")
      ->indent()
      ->write("print '<li>' . htmlspecialchars(\$fierceTagListOption) . '</li>';\n")
      ->outdent()
      ->write("}\n");
    ;
    
    self::closeTag('ul');
    
    // add js
    $this->requireScript(FIERCE_SRC . 'scripts/tag-field.controller.js');
  }
}
