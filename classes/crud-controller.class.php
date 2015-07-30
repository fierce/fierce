<?

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class CrudController extends PageController
{
  public $entity = false;
  public $listFields = [];
  public $mainTpl = 'main-admin.tpl';
  
  public function __construct()
  {
    Auth::requireAdmin();
    
    if (!$this->displayName) {
      $this->displayName = 'Manage ' . $this->entity . 's';
    }
    
    parent::__construct();
  }
  
  public function defaultAction()
  {
    $entity = $this->entity;
    $items = $entity::all('modified');
    
    $displayField = array_keys($this->listFields)[0];
    
    $this->display('crud-list.tpl', get_defined_vars());
  }
  
  public function addAction()
  {
    $entity = $this->entity;
    $item = $entity::createNew();
    
    $this->beforeEditOrAdd($item);
    
    $formType = 'Add';

    $this->display($this->editTpl, get_defined_vars());
  }
  
  public function addSubmitAction()
  {
    $entity = $this->entity;
    $item = $entity::createNew();

    $item->setData($_POST);
    $item->save();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url());
  }
  
  public function editAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $this->beforeEditOrAdd($item);
    
    $formType = 'Edit';
    
    $this->display($this->editTpl, get_defined_vars());
  }
  
  public function beforeEditOrAdd($item)
  {
    
  }
  
  public function editSubmitAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $item->setData($_POST);
    $item->save();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url());
  }
  
  public function deleteAction()
  {
    $entity = $this->entity;
    $item = $entity::createById(@$_GET['id']);
    
    $item->archive();
    
    $this->afterSave($item);
    
    HTTP::redirect($this->url());
  }
  
  public function afterSave($item)
  {
  }
}
