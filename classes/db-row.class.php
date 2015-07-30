<?

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class DBRow
{
  protected $id;
  protected $row;
  
  public static function all($sort=null)
  {
    global $db;
    $entity = strtolower(get_called_class());
    
    $rows = $db->$entity->find([], '-modified');
    
    $items = array();
    foreach ($rows as $id => $row) {
      $item = new $entity();
      $item->id = $id;
      $item->setData($row);
      
      $items[] = $item;
    }
    
    return $items;
  }
  
  public static function createById($id)
  {
    global $db;
    $entity = strtolower(get_called_class());
    
    $id = preg_replace('/[^a-z0-9-]/', '', $id);
    
    $row = $db->$entity->byId($id);
    
    $item = new $entity();
    $item->id = $id;
    $item->setData($row);
    
    return $item;
  }
  
  public static function createNew()
  {
    global $db;
    $entity = strtolower(get_called_class());
    
    $item = new $entity();
    $item->id = $db->id();
    $item->setData([]);
    
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
    $entity = strtolower(get_called_class());
    
    
    // misc fields
    $user = Auth::loggedInUser();
    $this->row->modified_by = $user->id;
    $this->row->modified = time();
    
    // save
    $db->$entity->archive($this->id);
    $db->$entity->write($this->id, $this->row);
    
  }
  
  public function archive()
  {
    global $db;
    $entity = strtolower(get_called_class());
    
    $db->$entity->archive($this->id);
  }  
}