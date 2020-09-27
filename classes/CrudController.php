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
  
  public $editReturnToList = false;
  
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
    $entity = $this->entity;
    $item = $entity::createNew();
    $formData = new FormData($this->editFields);
    
    $this->errorMessages = false;
    if (@$_GET['errors']) {
      $this->formErrors = json_decode($_GET['errors']);
    }
    
    if (@$_GET['form']) {
      $formData->fromStringArray(json_decode($_GET['form'], true));
    }
    
    $this->beforeEditOrAdd($item, $formData);
    
    $formType = 'Add';
    $formAction = $this->url('add-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Add ' . $this->noun;
    
    $this->display($this->editTpl, get_defined_vars());
  }
  
  public function addSubmitAction()
  {
    $db = Env::get('db');
    $db->begin();
    
    $entity = $this->entity;
    $item = $entity::createNew($_GET['id']);
    
    $formData = new FormData($this->editFields);
    $formData->retrieve();
    
    $formData->validate(function($errors) use ($formData) {
      $errorsJson = json_encode($errors);
      $formDataJson = json_encode($formData->toStringArray());
      
      HTTP::redirect($this->url('add', ['errors' => $errorsJson, 'form' => $formDataJson]));
    });
    
    $this->beforeSave($item, $formData);
    
    $item->setData($formData->getValues());
    $item->save();
    
    $this->afterSave($item);
    
    $db->commit();
    HTTP::redirect($this->url());
  }
  
  public function editAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $formData = new FormData($this->editFields);
    $formData->setValues($item);
    
    $this->errorMessages = false;
    if (@$_GET['errors']) {
      $this->formErrors = json_decode($_GET['errors']);
    }
    
    if (@$_GET['form']) {
      $formData->fromStringArray(json_decode($_GET['form'], true));
    }
    
    $this->beforeEditOrAdd($item, $formData);
    
    $formType = 'Edit';
    $formAction = $this->url('edit-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Edit ' . $this->noun;
    
    $message = @$_GET['message'];
    
    $this->display($this->editTpl, get_defined_vars());
  }
  
  public function items()
  {
    $entity = $this->entity;
    
    $items = $entity::all('-modified');
    
    return $items;
  }
  
  public function beforeEditOrAdd($item, $formData)
  {
    
  }
  
  public function editSubmitAction()
  {
    $db = Env::get('db');
    $db->begin();
    
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $formData = new FormData($this->editFields);
    $formData->retrieve();
    
    $formData->validate(function($errors) use ($formData, $item) {
      $errorsJson = json_encode($errors);
      $formDataJson = json_encode($formData->toStringArray());
      
      HTTP::redirect($this->url('edit', ['errors' => $errorsJson, 'form' => $formDataJson, 'id' => $item->id]));
    });
    
    $this->beforeSave($item, $formData);

    $item->setData($formData->getValues());    
      
    $item->save();
    
    $this->afterSave($item);
    
    $db->commit();
    
    if ($this->editReturnToList) {
      HTTP::redirect($this->url());
    } else {
      HTTP::redirect($this->url('edit', ['id' => $item->id, 'message' => 'Changes Saved']));
    }
  }
  
  public function deleteAction()
  {
    $db = Env::get('db');
    $db->begin();
    
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $this->beforeDelete($item);
    
    $item->archive();
    $item->purge();
    
    $this->afterDelete($item);
    
    $db->commit();
    HTTP::redirect($this->url());
  }
  
  public function beforeSave($item, FormData &$formData)
  {
  }
  
  public function afterSave($item)
  {
  }
  
  public function beforeDelete($item)
  {
  }
  
  public function afterDelete($item)
  {
  }
  
  public function breadcrumbs()
  {
    $breadcrumbs = [
      ['url' => $this->url(), 'name' => $this->nounPlural]
    ];
    
    if ($this->action == 'edit') {
      $entity = $this->entity;
      $item = $entity::createById($_GET['id']);
      
      $breadcrumbs[] = [
        'url' => $this->url('edit', ['id' => $item->id]),
        'name' => 'Edit ' . ($item->title ? $item->title : $this->noun)
      ];
    }
    
    if ($this->action == 'add') {
      $breadcrumbs[] = [
        'url' => $this->url('add'),
        'name' => 'Add' . $this->noun
      ];
    }
    
    return $breadcrumbs;
  }
}
