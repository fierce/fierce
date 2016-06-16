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

class NewsViewPostController extends PageController
{
  public function defaultAction()
  {
    $post = NewsPost::createById(@$_GET['id']);
    
    $tpl = 'news-view-post.tpl';
    $vars = [
      'post' => $post
    ];
    
    if (isset($this->options['content_only']) && $this->options['content_only']) {
      View::renderTpl($tpl, $vars);
      return;
    }
    
    View::main($this->mainTpl, $tpl, $vars);
  }
}
