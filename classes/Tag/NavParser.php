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

class NavParser extends \Twig_TokenParser
{
  public function parse(\Twig_Token $token)
  {
    $parser = $this->parser;
    $stream = $parser->getStream();
    
    $navIdentifier = 'main';
    if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
      $navIdentifier = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
    }
    
    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
    
    return new NavNode($token->getLine(), $this->getTag(), $navIdentifier);
  }
  
  
  public function getTag()
  {
    return 'nav';
  }
}
