<?

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

class Parser extends \Twig_TokenParser
{
  protected $tagName;
  protected $nodeClass;
  
  public function __construct($tagName, $nodeClass)
  {
    $this->tagName = $tagName;
    $this->nodeClass = $nodeClass;
  }
  
  public function parse(\Twig_Token $token)
  {
    $parser = $this->parser;
    $stream = $parser->getStream();
    
    $nodeClass = $this->nodeClass;
    
    $attributes = [];
    $nodes = [];
    
    // check for a value expression
    if ($nodeClass::$singleValue) { // check for a single value expression
      $nodes['value'] = $parser->getExpressionParser()->parseExpression();
    }
    
    // check for key/value pairs
    if ($nodeClass::$keyValuePairs) {
      while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
        $key = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
        $value = $this->parser->getExpressionParser()->parseExpression();
        
        $nodes[$key] = $value;
      }
    } 
    
    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
    
    $attributes = [];
    
    return new $nodeClass($nodes, $attributes, $token->getLine(), $this->getTag());
  }
  
  
  public function getTag()
  {
    return $this->tagName;
  }
}
