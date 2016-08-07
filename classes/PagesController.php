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
    'content',
    'nav'
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
    $this->prepareSidebarList();
    
    $displayField = array_keys($this->listFields)[0];
    
    $this->pageTitle = $this->nounPlural;
    
    $item = false;
    $crudContentTpl = false;
    $this->display('admin-pages.tpl', get_defined_vars());
  }
  
  public function updatePositionsAction()
  {
    $positionsByCategory = $_POST['page_list'];
    
    foreach ($positionsByCategory as $nav => $positionsJson) {
      $positions = json_decode($positionsJson);
      
      foreach ($positions as $position) {
        $page = Page::createById($position->id);
        
        $page->setData([
          'nav' => $nav,
          'navPositionLeft' => $position->position,
          'navPositionRight' => $position->position_right,
          'navPositionDepth' => $position->depth
        ]);
        
        $page->save();
      }
    }
    
    HTTP::redirect($_GET['return']);
  }
  
  public function addAction()
  {
    $entity = $this->entity;
    $item = $entity::createNew();
    $formData = new FormData($this->editFields);
    
    $formData->setValues([
      'nav' => isset($_GET['category']) ? $_GET['category'] : 'not_linked'
    ]);
    
    $this->beforeEditOrAdd($item, $formData);
    
    $formType = 'Add';
    $formAction = $this->url('add-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Add ' . $this->noun;
    
    $this->prepareSidebarList();
    
    $displayField = array_keys($this->listFields)[0];
    
    $crudContentTpl = $this->editTpl;
    $this->display('admin-pages.tpl', get_defined_vars());
  }
  
  public function addSubmitAction()
  {
    $entity = $this->entity;
    $item = $entity::createNew();
    
    $postData = $_POST;
    $this->beforeSave($item, $postData);
    
    // set nav position
    $largestPosition = 0;
    $existingPages = \Fierce\Page::all();
    foreach ($existingPages as $existingPage) {
      if (@$existingPage->nav != $postData['nav']) {
        continue;
      }
      
      if ($existingPage->navPositionRight > $largestPosition) {
        $largestPosition = $existingPage->navPositionRight;
      }
    }
    $postData['navPositionLeft'] = $largestPosition + 1;
    $postData['navPositionRight'] = $largestPosition + 2;
    $postData['navPositionDepth'] = 0;
    
    $item->setData($postData);
    $item->save();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url('edit', ['id' => $item->id]));
  }
  
  public function editAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $formData = new FormData($this->editFields);
    $formData->setValues($item);
    
    $this->beforeEditOrAdd($item, $formData);
    
    $formType = 'Edit';
    $formAction = $this->url('edit-submit', ['id' => $item->id]);
    
    $this->pageTitle = 'Edit ' . $this->noun;
    
    $this->prepareSidebarList();
    
    $displayField = array_keys($this->listFields)[0];
    
    $crudContentTpl = $this->editTpl;
    $this->display('admin-pages.tpl', get_defined_vars());
  }
  
  public function prepareSidebarList()
  {
    $entity = $this->entity;
    
    $items = $entity::all('navPositionLeft');
    
    $itemsByCategory = [];
    $itemsByCategory['main'] = (object)['name' => 'Main Navigation', 'items' => []];
    
    Env::set('additional_nav_categores', [], -1);
    $additionalCats = Env::get('additional_nav_categores');
    foreach (Env::get('additional_nav_categores') as $identifier => $name) {
      $itemsByCategory[$identifier] = (object)['name' => $name, 'items' => []];
    }
    
    $itemsByCategory['not_linked'] = (object)['name' => 'Not Linked', 'items' => []];
    
    foreach ($items as $item) {
      if (!isset($item->nav) || !isset($itemsByCategory[@$item->nav])) {
        continue;
      }
      
      $itemsByCategory[$item->nav]->items[] = $item;
    }
    
    View::set('itemsByCategory', $itemsByCategory);
  }
  
  public function beforeEditOrAdd($item, $formData)
  {
    $formData->content = preg_replace_callback('#^{{.*}}$#m', function($input) {
      
      $tag = $input[0];
      $tag = '<p>' . $tag . '</p>';
      
      return $tag;
    }, $formData->content);
    
        $formData->content = preg_replace_callback('#^{%.*%}$#m', function($input) {
      
      $tag = $input[0];
      $tag = '<p>' . $tag . '</p>';
      
      return $tag;
    }, $formData->content);
    
    $formData->content = preg_replace_callback('#{{ Embed\\.page\\(\'(.+?)\'\\)\\|raw }}#m', function($input) {
      
      $url = $input[1];
      
      return '[embed ' . $url . ']';
    }, $formData->content);
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
  
  public function beforeSave($item, &$postData)
  {
    if (!$item->class) {
      $item->class = '\Fierce\PageController';
    }
    
    $content = $postData['content'];
    
    $content = preg_replace_callback('#\\[embed (.+?)\\]#', function($input) {
      
      $url = trim($input[1]);
      
      $tag = '{{ Embed.page(\'' . $url . '\')|raw }}';
      
      return $tag;
    }, $content);
    
    $content = str_replace("\r\n", "\n", $content);
    $content = preg_replace_callback('#^<p>({{.*}})</p>$#m', function($input) {
      
      $tag = $input[1];
      $tag = str_replace('&nbsp;', ' ', $tag); // html_entity_decode does stupid shit with spaces
      $tag = html_entity_decode($tag, ENT_HTML5, 'UTF-8');
      
      return $tag;
    }, $content);
    $content = preg_replace_callback('#^<p>({%.*%})</p>$#m', function($input) {
      
      $tag = $input[1];
      $tag = str_replace('&nbsp;', ' ', $tag); // html_entity_decode does stupid shit with spaces
      $tag = html_entity_decode($tag, ENT_HTML5, 'UTF-8');
      
      return $tag;
    }, $content);
    
    
    

    
    $postData['content'] = $content;
  }
  
  public function deleteAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $item->archive();
    $item->purge();
    
    HTTP::redirect($this->url());
  }
}
