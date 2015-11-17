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
  
  static public function run($page, $db=false, $options=[])
  {
    $controllerClass = get_called_class();
    $controller = new $controllerClass();
    $controller->page = $page;
    $controller->db = $db;
    
    $action = @$_GET['do'];
    if (!$action) {
      $action = 'default';
    }
    $action = preg_replace('/[^a-z0-9_]+/', ' ', $action);
    $action = ucwords($action);
    $action = str_replace(' ', '', $action);
    $action[0] = strtolower($action[0]);
    $action .= 'Action';
    
    $controller->options = $options;
    
    $controller->$action();
  }
  
  public function display($tpl, $vars=[])
  {
    $vars = array_merge([
      'controller' => $this,
      'page' => $this->page,
      'pageTitle' => $this->page->name
    ], get_object_vars($this), $vars);
        
    if (isset($this->options['content_only']) && $this->options['content_only']) {
      View::renderTpl($tpl, $vars);
      return;
    }

    View::main($this->mainTpl, $tpl, $vars);
  }
  
  public function url($action=false, $params=array(), $absolute=false)
  {
    $url = Env::get('request_url');
    $url = parse_url($url, PHP_URL_PATH);
    $url = ltrim($url, '/');
    
    
    if ($action) {
      $params['do'] = $action;
    }
    
    if (count($params) > 0) {
      $url .= '?' . http_build_query($params);
    }
    
    if ($absolute) {
      $url = Env::get('base_url') . $url;
    }
    
    return $url;
  }
  
  public function defaultAction()
  {
    if ($this->page->url == '/404') {
      header('HTTP/1.0 404 Not Found');
      ResponseCache::disable();
    }
    
    if (isset($this->page->main_tpl)) {
      $this->mainTpl = $this->page->main_tpl;
    }
    
    $tplVars = [
      'controller' => $this,
      'pageTitle' => $this->page->name,
      'contentViewTpl' => $this->page->content,
      'page' => $this->page
    ];
    
    if (isset($this->options['content_only']) && $this->options['content_only']) {
      View::renderString($this->page->content, $tplVars);
      return;
    }
    
    View::main($this->mainTpl, false, $tplVars);
  }
}
