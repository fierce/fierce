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

class MigrationsController extends PageController
{
  public $mainTpl = 'main-admin.tpl';
  
  public function __construct()
  {
    Auth::requireAdmin();
    
    parent::__construct();
  }
  
  function defaultAction()
  {
    global $db;
    
    $files = glob(BASE_PATH . 'migrations/*.php');
    sort($files, SORT_NATURAL);
    
    print '<html><style>body {font-family: monospace; font-size: 12px}</style><body>';
    
    foreach ($files as $file) {
      require_once $file;
      
      $class = strtolower(pathinfo($file, PATHINFO_FILENAME));
      $class = preg_replace('/[^a-z0-9]/', '', $class);
      
      if ($db->completed_migrations->idExists(sha1($class))) {
        continue;
      }
      
      print '<h3>' . basename($file) . '</h3>';
      
      $migration = new $class();
      $migration->run();
      
      $db->completed_migrations->write(sha1($class), (object)[
        'date' => date('Y-m-d H:i:s')
      ]);
    }
        
    exit;
  }
}