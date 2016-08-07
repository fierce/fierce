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
  public $htmlOutput = true;
  
  public $mainTpl = 'main-admin.tpl';
  
  function defaultAction()
  {
    if ($this->htmlOutput) {
      print '<html><style>body {font-family: monospace; font-size: 12px}</style><body>';
    }
    
    $count = 0;
    
    $files = glob(Env::get('fierce_path') . 'migrations/*.php');
    sort($files, SORT_NATURAL);
    foreach ($files as $file) {
      if ($this->runMigrationFile($file)) {
        $count++;
      }
    }
    
    $files = glob(Env::get('base_path') . 'migrations/*.php');
    sort($files, SORT_NATURAL);
    foreach ($files as $file) {
      if ($this->runMigrationFile($file)) {
        $count++;
      }
    }
    
    if ($this->htmlOutput) {
      print '<p>';
    }
    print 'Finished running ' . $count . ' migrations.';
    if ($this->htmlOutput) {
      print '</p>';
    }
        
    exit;
  }
  
  function runMigrationFile($file)
  {
    $db = Env::get('db');
    
    if ($db->connection->entityExists('CompletedMigration') &&
        $db->CompletedMigration->idExists(sha1(basename($file)))) {
      return false;
    }
    
    if ($this->htmlOutput) {
      print '<h3>';
    }
    print basename($file);
    if ($this->htmlOutput) {
      print '</h3>';
    }
    
    require_once $file;
    
    $db->CompletedMigration->write(sha1(basename($file)), (object)[
      'file' => strtolower(pathinfo($file, PATHINFO_FILENAME)),
      'date' => new \DateTime()
    ]);
    
    return true;
  }
}
