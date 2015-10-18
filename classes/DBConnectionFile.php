<?php

namespace Fierce;

class DBConnectionFile
{
  public $dataDir;
  
  public function __construct($dataDir)
  {
    $this->dataDir = $dataDir;
  }
  
  public function find($entity, $params, $orderBy, $range)
  {
    $files = glob("$this->dataDir/$entity/*.json");
    
    $rows = array();
    foreach ($files as $file) {
      $id = pathinfo($file, PATHINFO_FILENAME);
      
      $rows[$id] = json_decode(file_get_contents($file));
    }
    
    if ($orderBy) {
      $asc = ($orderBy[0] != '-');
      $key = trim($orderBy, '-+');
      
      uasort($rows, function($a, $b) use ($asc, $key) {
        if ($asc) {
          return strnatcmp(@$a->$key, @$b->$key);
        } else {
          return strnatcmp(@$b->$key, @$a->$key);
        }
      });
    }
    
    return $rows;
  }
  
  public function byId($entity, $id)
  {
    $file = "$this->dataDir/$entity/$id.json";
    if (!file_exists($file)) {
      throw new \exception('Invalid ID');
    }
    
    return json_decode(file_get_contents($file));
  }
  
  public function idExists($entity, $id)
  {
    $file = "$this->dataDir/$entity/$id.json";
    return file_exists($file);
  }
  
  public function write($entity, $id, $row, $allowOverwrite)
  {
    $file = "$this->dataDir/$entity/$id.json";
    
    if (!$allowOverwrite && file_exists($file)) {
      throw new \exception('cannot write to ' . $this->entity . ':' . $id . ' – already exists.');
    }
    
    file_put_contents($file, json_encode($row, JSON_PRETTY_PRINT));
  }
  
  public function archive($entity, $id)
  {
    $file = "$this->dataDir/$entity/$id.json";
    if (!file_exists($file)) {
      return;
    }
    
    $timestamp = time();
    $archivefile = "$this->dataDir/$entity/archive/$timestamp-$id.json";
    
    rename($file, $archivefile);
  }
  
  public function purge($entity, $id)
  {
    $file = "$this->dataDir/$entity/$id.json";
    
    if (file_exists($file)) {
      unlink($file);
    }
  }
}

