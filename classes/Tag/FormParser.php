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

class FormParser extends \Twig_TokenParser
{
  public function parse(\Twig_Token $token)
  {
    $lineno = $token->getLine();
    $stream = $this->parser->getStream();
    
    $attributes = [];
    $nodes = [];
    while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
      $key = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
      $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
      $value = $this->parser->getExpressionParser()->parseExpression();
      
      $nodes[$key] = $value;
    }
    
    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
    $nodes['body'] = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
    
    return new FormNode($nodes, $attributes, $lineno, $this->getTag());
    
    // $name = 'form';
//     $block = new FormNode($nodes, $attributes, new \Twig_Node(array()), $lineno);
//     $this->parser->setBlock($name, $block);
//     $this->parser->pushLocalScope();
//     $this->parser->pushBlockStack($name);
// 
//     if ($stream->nextIf(\Twig_Token::BLOCK_END_TYPE)) {
//         $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
//         if ($token = $stream->nextIf(\Twig_Token::NAME_TYPE)) {
//             $value = $token->getValue();
// 
//             if ($value != $name) {
//                 throw new \Twig_Error_Syntax(sprintf('Expected endblock for block "%s" (but "%s" given)', $name, $value), $stream->getCurrent()->getLine(), $stream->getFilename());
//             }
//         }
//     } else {
//         $body = new \Twig_Node(array(
//             new \Twig_Node_Print($this->parser->getExpressionParser()->parseExpression(), $lineno),
//         ));
//     }
//     $stream->expect(\Twig_Token::BLOCK_END_TYPE);
// 
//     $block->setNode('body', $body);
//     $this->parser->popBlockStack();
//     $this->parser->popLocalScope();
// 
//     return new \Twig_Node_BlockReference($name, $lineno, $this->getTag());
  }
  
  public function getTag()
  {
    return 'form';
  }
  
  public function decideBlockEnd(\Twig_Token $token)
  {
    return $token->test('endform');
  }
}
