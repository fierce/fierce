<?php

namespace Fierce;

class MediaController extends PageController
{
  public $mainTpl = 'main-admin.tpl';
  
  public function __construct()
  {
    Auth::requireAdmin();
    
    if (!$this->displayName) {
      $this->displayName = 'Manage Images';
    }
    
    parent::__construct();
  }
  
  public function defaultAction()
  {
    $imageFiles = glob(Env::get('base_path') . 'images/*');
    $imageItems = array();
    foreach ($imageFiles as $file) {
      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      if (!in_array($ext, ['png', 'jpg', 'svg', 'gif'])) {
        continue;
      }
      
      $imageItems[] = (object)[
        'name' => basename($file),
        'path' => $file,
        'url' => str_replace(Env::get('base_path'), '', $file)
      ];
    }
    
    View::addCss(Env::get('fierce_src') . 'css/admin-media.css');
    View::addScript(Env::get('fierce_src') . 'scripts/admin-media.js');

    
    $pageTitle = 'Media';
    
    $this->display('media-list.tpl', get_defined_vars());
  }
  
  public function uploadAction()
  {
    $imageData = file_get_contents('php://input'); // read POST
    $imageData = file_get_contents($imageData); // decode data-uri
    
    $name = $_GET['name'];
    $name = preg_replace('/[^a-zA-Z0-9-_.]+/', '-', $name);
    
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
      die('Invalid file type');
    }
    
    $path = Env::get('base_path') . 'images/' . $name;
    
    file_put_contents($path, $imageData);
    
    die('success: images/' . $name);
  }
}
