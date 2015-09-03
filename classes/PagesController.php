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
  
  public $editFields = [
    'name',
    'url',
    'content'
  ];
  
  public $categoryField = 'admin_category';
  
  public function __construct()
  {
    parent::__construct();
  }
  
  public function items()
  {
    global $db;
    
    $entity = $this->entity;
    
    $items = $db->$entity->getIndexRows('crud');
    if (!$items) {
      $items = $entity::all('nav_position');
    }
    
    return $items;
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
