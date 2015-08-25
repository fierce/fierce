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

class PageController
{
  public $page;
  public $displayName = null;
  public $mainTpl = 'main-public.tpl';
  
  public function __construct()
  {
    if (!$this->displayName) {
      $this->displayName = get_class($this);
      $this->displayName = preg_replace('/Controller$/', '', $this->displayName);
      $this->displayName = preg_replace('/^.+\\\/', '', $this->displayName);
    }
  }
  
  static public function run($page)
  {
    $controllerClass = get_called_class();
    $controller = new $controllerClass();
    $controller->page = $page;
    
    $action = preg_replace('/[^a-z0-9_]/', '', strtolower(@$_GET['do']));
    if (!$action) {
      $action = 'default';
    }
    $action .= 'Action';
    
    $controller->$action();
  }
  
  public function display($tpl, $vars)
  {
    $vars = array_merge(array('controller' => $this), get_object_vars($this), $vars);
    
    View::main($this->mainTpl, $tpl, $vars);
  }
  
  public function url($action=false, $params=array(), $absolute=false)
  {
    $url = REQUEST_URL;
    $url = parse_url($url, PHP_URL_PATH);
    $url = ltrim($url, '/');
    
    
    if ($action) {
      $params['do'] = $action;
    }
    
    if (count($params) > 0) {
      $url .= '?' . http_build_query($params);
    }
    
    if ($absolute) {
      $url = BASE_URL . $url;
    }
    
    return $url;
  }
  
  public function defaultAction()
  {
    if ($this->page->url == '/404') {
      header('HTTP/1.0 404 Not Found');
      ResponseCache::disable();
    }
    
    View::main($this->mainTpl, false, [
      'pageTitle' => $this->page->name,
      'contentViewHtml' => $this->page->content
    ]);
  }
}
