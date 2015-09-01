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

    $stream->expect(\Twig_Token::BLOCK_END_TYPE);

    return new NavNode($token->getLine(), $this->getTag());
  }
  
  
  public function getTag()
  {
    return 'nav';
  }
}
