<?

namespace Fierce;


require realpath('../../../../../../autoload.php');
require realpath('../../../../init.php');

class CKEditorMediaController extends MediaController
{
  public $mainTpl = 'main-blank.tpl';
  public $displayName = 'Select Image';
  
  public function defaultAction()
  {
    View::addScript(Env::get('fierce_src') . 'third-party/ckeditor/plugins/fierce-image-select/scripts/ckeditor-media.controller.js');
    
    parent::defaultAction();
  }
}

CKEditorMediaController::run(false);
