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
      if (!in_array(pathinfo($file, PATHINFO_EXTENSION), ['png', 'jpg', 'svg', 'gif'])) {
        continue;
      }
      
      $imageItems[] = (object)[
        'name' => basename($file),
        'path' => $file,
        'url' => str_replace(Env::get('base_path'), '', $file)
      ];
    }
    
    $this->display('media-list.tpl', get_defined_vars());
  }
}
