<?

class MediaController extends PageController
{
  public $mainTpl = 'main-admin.tpl';
  
  public function __construct()
  {
    Auth::requireAdmin();
    
    if (!$this->displayName) {
      $this->displayName = 'Manage Images/CSS/JavaScript';
    }
    
    parent::__construct();
  }
  
  public function defaultAction()
  {
    $imageFiles = glob(BASE_PATH . 'images/*');
    $imageItems = array();
    foreach ($imageFiles as $file) {
      if (!in_array(pathinfo($file, PATHINFO_EXTENSION), ['png', 'jpg', 'svg', 'gif'])) {
        continue;
      }
      
      $imageItems[] = (object)[
        'name' => basename($file),
        'path' => $file,
        'url' => str_replace(BASE_PATH, '', $file)
      ];
    }
    
    $cssFiles = glob(BASE_PATH . 'css/*.css');
    $cssItems = array();
    foreach ($cssFiles as $file) {
      $cssItems[] = (object)[
        'name' => basename($file),
        'path' => $file,
        'url' => str_replace(BASE_PATH, '', $file)
      ];
    }
    
    $jsFiles = glob(BASE_PATH . 'scripts/*.js');
    $jsItems = array();
    foreach ($jsFiles as $file) {
      $jsItems[] = (object)[
        'name' => basename($file),
        'path' => $file,
        'url' => str_replace(BASE_PATH, '', $file)
      ];
    }
    
    $this->display('media-list.tpl', get_defined_vars());
  }
}
