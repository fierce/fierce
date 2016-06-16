<?php

namespace Fierce;

class AdminNewsController extends CrudController
{
  public $entity = 'Fierce\NewsPost';
  public $noun = 'News Post';
  
  public $listFields = [
    'title' => 'Title',
    'date' => 'Date'
  ];
  
  public $editFields = [
    'title',
    ['name' => 'date', 'type' => 'date'],
    ['name' => 'content', 'displayName' => 'Post']
  ];
  
  public $editTpl = 'news-post-edit.tpl';
  
  public function beforeEditOrAdd($item, $formData)
  {
    if (!$item->modified && !$item->date) {
      $formData->date = new \DateTime();
    }
  }
  
  public function items()
  {
    $entity = $this->entity;
    
    $items = $entity::all('-date');
    
    return $items;
  }
}
