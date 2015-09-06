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
  
  public $editFields = [
    'name',
    'url',
    'content'
  ];
  
  public $noun = false;
  public $nounPlural = false;
  
  public $mainTpl = 'main-admin.tpl';
  
  public function __construct()
  {
    Auth::requireAdmin();
    
    if (!$this->noun) {
      $this->noun = preg_replace('/^.+\\\/', '', $this->entity);
    }
    if (!$this->nounPlural) {
      $this->nounPlural = $this->noun . 's';
    }
    
    if (!$this->displayName) {
      $this->displayName = 'Manage ' . $this->nounPlural;
    }
    
    parent::__construct();
  }

  public function defaultAction()
  {
    $itemsByCategory = $this->itemsByCategory();
    
    $displayField = array_keys($this->listFields)[0];
    
    $this->pageTitle = $this->nounPlural;
    
    $item = false;
    $crudContentTpl = false;
    $this->display('crud-sidebar.tpl', get_defined_vars());
  }
  
  public function addAction()
  {
    global $db;
    
    $entity = $this->entity;
    $item = $entity::createNew();
    $formData = new FormData($this->editFields);
    
    $item->admin_category = @$_GET['category'];
    
    $this->beforeEditOrAdd($item);
    
    $formType = 'Add';
    $formAction = $this->url('add-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Add ' . $this->noun;
    
    $itemsByCategory = $this->itemsByCategory();
    
    $displayField = array_keys($this->listFields)[0];
    
    $crudContentTpl = $this->editTpl;
    $this->display('crud-sidebar.tpl', get_defined_vars());
  }
  
  public function addSubmitAction()
  {
    $entity = $this->entity;
    $item = $entity::createNew();
    
    $postData = $_POST;
    $this->beforeSave($item, $postData);
    
    $item->setData($postData);
    $item->save();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url('edit', ['id' => $item->id]));
  }
  
  public function editAction()
  {
    global $db;
    
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $formData = new FormData($this->editFields);
    $formData->setValues($item);
    
    $this->beforeEditOrAdd($item);
    
    $formType = 'Edit';
    $formAction = $this->url('edit-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Edit ' . $this->noun;
    
    $itemsByCategory = $this->itemsByCategory();
    
    $displayField = array_keys($this->listFields)[0];
    
    $crudContentTpl = $this->editTpl;
    $this->display('crud-sidebar.tpl', get_defined_vars());
  }
  
  public function itemsByCategory()
  {
    global $db;
    
    $entity = $this->entity;
    
    $items = $entity::all('nav_position');
    
    $itemsByCategory = [
      'main' => (object)['name' => 'Main Navigation', 'items' => []],
      'not_linked' => (object)['name' => 'Not Linked', 'items' => []]
    ];
    
    foreach ($items as $item) {
      if (!isset($itemsByCategory[$item->admin_category])) {
        continue;
      }
      
      $itemsByCategory[$item->admin_category]->items[] = $item;
    }
    
    return $itemsByCategory;
  }
  
  public function beforeEditOrAdd($item)
  {
    global $autoloadClasses;
    
    $classOptions = [
      'Fierce\PageController' => 'Plain Page'
    ];
        
    View::set('classOptions', $classOptions);
  }
  
  public function editSubmitAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $postData = $_POST;
    $this->beforeSave($item, $postData);
    
    $item->setData($postData);
    $item->save();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url('edit', ['id' => $item->id]));
  }
  
  public function afterSave($item)
  {
    ResponseCache::flushAll();
  }
  
  public function deleteAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $item->archive();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url());
  }
  
  public function beforeSave($item, &$data)
  {
  }
  

}
