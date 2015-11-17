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

class DateFieldNode extends FieldNode
{
  public static $tagName = 'date_field';
  
  public function valueNode()
  {
    // date value (either a DateTimeImmutable object, or null)
    $valueNode = parent::valueNode();
    
    // format('Y-m-d') method call on date object
    $args = new \Twig_Node_Expression_Array([], $this->lineno);
    $args->addElement(new \Twig_Node_Expression_Constant('j M, Y', $this->lineno));
    
    $formatNode = new \Twig_Node_Expression_MethodCall($valueNode, 'format', $args, $this->lineno);
    
    // conditional to check for a null value
    $conditionalNode = new \Twig_Node_Expression_Conditional($valueNode, $formatNode, $valueNode, $this->lineno);

    return $conditionalNode;
  }
  
  public function compileTag()
  {
    if (!$this->hasNode('class')) {
      $this->setNode('class', new \Twig_Node_Expression_Constant('date_field', $this->lineno));
    }
    
    parent::compileTag();
    
    $this->requireCss(\Fierce\Env::get('fierce_src') . 'css/date-field.css');
    $this->requireScript(\Fierce\Env::get('fierce_src') . 'scripts/calendar-field.controller.js');
  }
}
