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
  
  public static function tableName()
  {
    $class = get_called_class();
    return preg_replace('/(.*)\\\\/', '', $class);
  }
  
  static public function all($sort=null)
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
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
  
  public static function find($params=[], $sort=null, $range=null)
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $rows = $db->$entity->find($params, $sort, $range);
    
    $items = array();
    foreach ($rows as $id => $row) {
      $item = new $class();
      $item->id = $id;
      $item->setData($row);
      
      $items[] = $item;
    }
    
    return $items;
  }
  
  static public function idExists($id)
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);
    
    return $db->$entity->idExists($id);
  }
  
  static public function createById($id)
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);
    
    $row = $db->$entity->byId($id);
    
    $item = new $class();
    $item->id = $id;
    $item->setData($row);
    
    return $item;
  }
  
  static public function createNew()
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $row = $db->$entity->blankRow();
    $row->id = $db->id();
    
    $item = new $class();
    $item->setData($row);
    $item->id = $row->id;
    
    return $item;
  }
  
  static public function createByFind($params)
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $rows = self::find($params);
    if (count($rows) == 0) {
    	throw new \exception("Cannot find row matching params");
    }
    if (count($rows) > 1) {
    	throw new \exception("Found too many rows matching params");
    }
    
    return array_shift($rows);
  }
  
  public function __get($key)
  {
    switch ($key) {
      case 'id':
        return $this->id;
      case 'row':
        return $this->row;
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
      if (isset($this->$key)) {
        $this->$key = $value;
      } else {
        $this->row->$key = $value;
      }
    }
  }
  
  public function save()
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    // misc fields
    $user = Auth::loggedInUser();
    if ($user) {
      $this->row->modifiedBy = $user->id;
    } else {
      $this->row->modifiedBy = 'none';
    }
    $this->row->modified = new \DateTime();
    
    // save
    $db->$entity->archive($this->id);
    $db->$entity->write($this->id, $this->row, true);
    
  }
  
  public function archive()
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $db->$entity->archive($this->id);
  }
  
  public function purge()
  {
    $db = Env::get('db');
    $class = get_called_class();
    $entity = $class::tableName();
    
    $db->$entity->purge($this->id);
  }
}
