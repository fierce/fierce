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

namespace Fierce;

class NewsRecentController extends PageController
{
  public function defaultAction()
  {
    $posts = NewsPost::find([], '-date', [0, 10]);
    
    $tpl = 'news-recent.tpl';
    $vars = [
      'posts' => $posts
    ];
    
    if (isset($this->options['content_only']) && $this->options['content_only']) {
      View::renderTpl($tpl, $vars);
      return;
    }
    
    View::main($this->mainTpl, $tpl, $vars);
  }
}
