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

class DBRow
{
  public $id;
  protected $row;
  
  static public function all($sort=null)
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    $rows = $db->$entity->find([], $sort);
    
    $items = array();
    foreach ($rows as $id => $row) {
      $item = new $class();
      $item->id = $id;
      $item->setData($row);
      
      $items[] = $item;
    }
    
    return $items;
  }
  
  public static function find($params=[], $sort=null)
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    $rows = $db->$entity->find($params, $sort);
    
    $items = array();
    foreach ($rows as $id => $row) {
      $item = new $class();
      $item->id = $id;
      $item->setData($row);
      
      $items[] = $item;
    }
    
    return $items;
  }
  
  static public function createById($id)
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    $id = preg_replace('/[^a-z0-9-]/', '', $id);
    
    $row = $db->$entity->byId($id);
    
    $item = new $class();
    $item->id = $id;
    $item->setData($row);
    
    return $item;
  }
  
  static public function createNew()
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    $item = new $class();
    $item->setData($db->$entity->blankRow());
    $item->id = $db->id();
    
    return $item;
  }
  
  public function __get($key)
  {
    switch ($key) {
      case 'id':
        return $this->id;
    }
    
    return $this->row->$key;
  }
  
  public function __set($key, $value)
  {
    if (!property_exists($this->row, $key)) {
      throw new \exception("Cannot set $key on " . get_called_class());
    }
    
    $this->row->$key = $value;
  }
  
  public function __isset($key)
  {
    if (method_exists($this, $key)) {
      return true;
    }
    
    return property_exists($this->row, $key);
  }
  
  public function id()
  {
    return $this->id;
  }
  
  public function setData($data)
  {
    if (is_array($data)) {
      $data = (object)$data;
    }
    if (!is_object($data)) {
      $data = (object)[];
    }
    if (!$this->row) {
      $this->row = (object)[];
    }
    
    foreach ($data as $key => $value) {
      $this->row->$key = $value;
    }
  }
  
  public function save()
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    // misc fields
    $user = Auth::loggedInUser();
    $this->row->modified_by = $user->id;
    $this->row->modified = new \DateTimeImmutable();
    
    // save
    $db->$entity->archive($this->id);
    $db->$entity->write($this->id, $this->row, true);
    
  }
  
  public function archive()
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    $db->$entity->archive($this->id);
  }
  
  public function purge()
  {
    global $db;
    $class = get_called_class();
    $entity = preg_replace('/(.*)\\\\/', '', strtolower($class));
    
    $db->$entity->purge($this->id);
  }
}
