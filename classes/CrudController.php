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

class CrudController extends PageController
{
  public $entity = false;
  public $listFields = ['name' => 'Name'];
  
  public $noun = false;
  public $nounPlural = false;
  
  public $mainTpl = 'main-admin.tpl';
  
  public $listTpl = 'crud-list.tpl';
  public $editTpl = false;
  
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
    $items = $this->items();
    
    $displayField = array_keys($this->listFields)[0];
    
    $this->pageTitle = $this->noun . ' List';
    
    $this->display($this->listTpl, get_defined_vars());
  }
  
  public function addAction()
  {
    global $db;
    
    $entity = $this->entity;
    $item = $entity::createNew();
    $formData = new FormData($this->editFields);
    
    $this->beforeEditOrAdd($item);
    
    $formType = 'Add';
    $formAction = $this->url('add-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Add ' . $this->noun;
    
    $this->display($this->editTpl, get_defined_vars());
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
    
    HTTP::redirect($this->url());
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
    
    $this->display($this->editTpl, get_defined_vars());
  }
  
  public function items()
  {
    global $db;
    
    $entity = $this->entity;
    
    $items = $db->$entity->getIndexRows('crud');
    if (!$items) {
      $items = $entity::all('modified');
    }
    
    return $items;
  }
  
  public function beforeEditOrAdd($item)
  {
    
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
  
  public function afterSave($item)
  {
  }
}
