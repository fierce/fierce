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

class PagesController extends CrudController
{
  public $entity = 'Fierce\Page';
  
  public $editTpl = 'page-edit.tpl'; 
  public $mode = 'sidebar';
  
  public $categories = [
    'main' => 'Main Navigation',
    'not_linked' => 'Not Linked'
  ];
  
  public $categoryField = 'admin_category';
  
  public function __construct()
  {
    parent::__construct();
  }
  
  public function beforeEditOrAdd($item)
  {
    global $autoloadClasses;
    
    $classOptions = [
      'Fierce\PageController' => 'Plain Page'
    ];
        
    View::set('classOptions', $classOptions);
  }
  
  public function afterSave($item)
  {
    ResponseCache::flushAll();
  }
}
