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
  static $dbEntity = null;
  static $dbEnvName = 'db';
  
  public $id;
  protected $row;
  protected $fetchCache = [];
  
  public static function tableName()
  {
    if (static::$dbEntity) {
      return static::$dbEntity;
    }
    
    return preg_replace('/(.*)\\\\/', '', get_called_class());
  }
  
  static public function all($sort=null)
  {
    $db = Env::get(static::$dbEnvName);
    $entity = static::tableName();
    
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
    $db = Env::get(static::$dbEnvName);
    $class = get_called_class();
    $entity = static::tableName();
    
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
    $db = Env::get(static::$dbEnvName);
    $entity = static::tableName();
    
    $id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);
    
    return $db->$entity->idExists($id);
  }
  
  static public function createById($id)
  {
    $db = Env::get(static::$dbEnvName);
    $class = get_called_class();
    $entity = static::tableName();
    
    $id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);
    
    $row = $db->$entity->byId($id);
    
    $item = new $class();
    $item->id = $id;
    $item->setData($row);
    
    return $item;
  }
  
  static public function createNew()
  {
    $db = Env::get(static::$dbEnvName);
    $class = get_called_class();
    $entity = static::tableName();
    
    $row = $db->$entity->blankRow();
    $row->id = $db->id();
    
    $item = new $class();
    $item->setData($row);
    $item->id = $row->id;
    
    return $item;
  }
  
  static public function createByFind($params)
  {
    $db = Env::get(static::$dbEnvName);
    $entity = static::tableName();
    
    $rows = self::find($params);
    if (count($rows) == 0) {
    	throw new \exception("Cannot find $entity where " . json_encode($params));
    }
    if (count($rows) > 1) {
    	throw new \exception("Found too many rows in $entity where " . json_encode($params));
    }
    
    return array_shift($rows);
  }
  
  /**
   * Property accessor for database entity. Several locations are searched, in this order:
   * 
   * - if a "getKey()" method exists it will be executed
   * - if a "fetchKey()" method exists it will be executed the first time and cached thereafter. unset($this->fetchCache[$key]) to clear
   * - for "id" the id of the row is returned (in future this may support dual primary keys)
   * - for "row" the raw row object is returned
   * - if the key is a database column in the entity it will be returned
   * - failing those, an exception is thrown
   */
  public function __get($key)
  {
    $getMethod = 'get' . $key;
    if (method_exists($this, $getMethod)) {
      return $this->$getMethod();
    }
    
    $fetchMethod = 'fetch' . $key;
    if (method_exists($this, $fetchMethod)) {
      if (!array_key_exists($key, $this->fetchCache)) {
        $this->fetchCache[$key] = $this->$fetchMethod();
      }
      return $this->fetchCache[$key];
    }
    
    switch ($key) {
      case 'id':
        return $this->id;
      case 'row':
        return $this->row;
    }
    
    if (property_exists($this->row, $key)) {
      return $this->row->$key;
    }
    
    throw new \exception("Cannot acccess $key on " . get_called_class());
  }
  
  public function __set($key, $value)
  {
    $setMethod = 'set' . $key;
    if (method_exists($this, $setMethod)) {
      return $this->$setMethod($value);
    }
    
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
    $db = Env::get(static::$dbEnvName);
    $entity = static::tableName();
    
    // misc fields
    if (property_exists($this->row, 'modifiedBy')) {
      $user = Auth::loggedInUser();
      if ($user) {
        $this->row->modifiedBy = $user->id;
      } else {
        $this->row->modifiedBy = 'none';
      }
    }
    if (property_exists($this->row, 'modified')) {
      $this->row->modified = new \DateTime();
    }
    
    // save
    $db->$entity->archive($this->id);
    $db->$entity->write($this->id, $this->row, true);
    
  }
  
  public function archive()
  {
    $db = Env::get(static::$dbEnvName);
    $entity = static::tableName();
    
    $db->$entity->archive($this->id);
  }
  
  public function purge()
  {
    $db = Env::get(static::$dbEnvName);
    $entity = static::tableName();
    
    $db->$entity->purge($this->id);
  }
}
