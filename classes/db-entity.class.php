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

class DBEntity
{
  protected $type;
  protected $entity;
  protected $dataDir;
  protected $archiveDir;
  
  public function __construct($db, $entity)
  {
    $this->type = $db->type;
    $this->entity = $entity;
    
    $this->dataDir = "{$db->dataPath}$this->entity/";
    $this->archiveDir = "$this->dataDir/archive/";
    
    if (!is_dir($this->archiveDir)) {
      mkdir($this->archiveDir, 0777, true);
    }
  }
  
  public function find($params = array(), $orderBy = null, $range = null)
  {
    $rows = array();
    
    switch ($this->type) {
      case 'file':
        $files = glob("$this->dataDir/*.json");
        
        
        foreach ($files as $file) {
          $id = pathinfo($file, PATHINFO_FILENAME);
          
          $rows[$id] = json_decode(file_get_contents($file));
        }
        
        if ($orderBy) {
          $asc = ($orderBy[0] != '-');
          $key = trim($orderBy, '-+');
          
          uasort($rows, function($a, $b) use ($asc, $key) {
            if ($asc) {
              return strcmp(@$a->$key, @$b->$key);
            } else {
              return strcmp(@$b->$key, @$a->$key);
            }
          });
        }
        
        break;
    }
    
    return $rows;
  }
  
  /**
   * Fetch a record by it's id.
   */
  public function byId($id)
  {
    $data = $this->rawDataById($id);
    
    return json_decode($data);
  }
  
  /**
   * faster version of byId() when you don't need an object
   */
  public function rawDataById($id)
  {
    switch ($this->type) {
      case 'file':
        $file = "$this->dataDir/$id.json";
        if (!file_exists($file)) {
          throw new exception('Invalid ID');
        }
        
        return file_get_contents($file);
    }
  }
  
  public function idExists($id)
  {
    switch ($this->type) {
      case 'file':
        $file = "$this->dataDir/$id.json";
        return file_exists($file);
    }
  }
  
  
  
  public function write($id, $row, $allowOverwrite = false)
  {
    $file = "$this->dataDir/$id.json";
    
    if (!$allowOverwrite && file_exists($file)) {
      throw new exception('cannot write to ' . $this->entity . ':' . $id . ' – already exists.');
    }
    
    file_put_contents($file, json_encode($row, JSON_PRETTY_PRINT));
  }
  
  public function archive($id)
  {
    $file = "$this->dataDir/$id.json";
    if (!file_exists($file)) {
      return;
    }
    
    $timestamp = time();
    $archivefile = "$this->dataDir/archive/$timestamp-$id.json";
    
    rename($file, $archivefile);
  }
  
  public function purge($id)
  {
    $file = "$this->dataDir/$id.json";
    
    if (file_exists($file)) {
      unlink($file);
    }
  }
}
