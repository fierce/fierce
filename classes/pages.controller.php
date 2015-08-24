<?

namespace F;

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class PagesController extends CrudController
{
  public $entity = 'F\Page';
  
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
      'F\PageController' => 'Plain Page'
    ];
    foreach ($autoloadClasses as $class => $file) {
      if (pathinfo(pathinfo($file, PATHINFO_FILENAME), PATHINFO_EXTENSION) != 'controller') {
        continue;
      }
      
      $controller = new $class();
      
      $classOptions[$class] = $controller->displayName;
    }
    
    View::set('classOptions', $classOptions);
  }
  
  public function afterSave($item)
  {
    ResponseCache::flushAll();
  }
}
