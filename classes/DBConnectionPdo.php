<?php

namespace Fierce;

class DBConnectionPdo
{
  public $pdo;
  protected $structures = [];
  
  public function __construct($dsn, $username, $password)
  {
    $this->pdo = new \PDO($dsn, $username, $password, [
    	\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ]);
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }
  
  public function find($entity, $params, $orderBy, $range)
  {
    if ($range) {
      throw new \exception('not yet implemented');
    }
    
    $sql = "
      SELECT * FROM `$entity`
      where 1
    ";
    
    $queryParams = [];
    foreach ($params as $column => $value) {
      $sql .= "
        and `$column` = :$column
      ";
      $queryParams[$column] = $value;
    }
    
    if ($orderBy) {
      $asc = ($orderBy[0] != '-');
      $key = trim($orderBy, '-+');
      
      $sql .= "ORDER BY `$key` " . ($asc ? "ASC\n" : "DESC\n");
    }
    
    $q = $this->pdo->prepare($sql);
    $q->execute($queryParams);
    
    $rows = [];
    while ($row = $q->fetch(\PDO::FETCH_OBJ)) {
      $structure = $this->entityStructure($entity);
      foreach ($structure as $field) {
        $fieldName = $field->name;
        
        switch ($field->type) {
          case 'date':
            if ($row->$fieldName !== null) {
              $row->$fieldName = new \DateTime($row->$fieldName);
            }
            break;
          case 'datetime':
            if ($row->$fieldName !== null) {
              $row->$fieldName = new \DateTime($row->$fieldName);
            }
            break;
          case 'int':
          case 'uint':
            $row->$fieldName = (int)$row->$fieldName;
            break;
        }
      }
      
      $rows[$row->id] = $row;
    }
    
    return $rows;
  }
  
  public function byId($entity, $id)
  {
    $q = $this->pdo->prepare("
      SELECT * FROM `$entity` where `id` = :id
    ");
    $q->execute(['id' => $id]);
    
    $row = $q->fetch(\PDO::FETCH_OBJ);
    
    if (!$row) {
    	throw new \exception("Invalid id $id on $entity");
    }
    
    $structure = $this->entityStructure($entity);
    foreach ($structure as $field) {
      $fieldName = $field->name;
      
      switch ($field->type) {
        case 'date':
          if ($row->$fieldName && $row->$fieldName != '0000-00-00') {
            $row->$fieldName = new \DateTime($row->$fieldName);
          }
          break;
        case 'datetime':
          if ($row->$fieldName && $row->$fieldName != '0000-00-00 00:00:00') {
            $row->$fieldName = new \DateTime($row->$fieldName);
          }
          break;
        case 'int':
        case 'uint':
          $row->$fieldName = (int)$row->$fieldName;
          break;
      }
    }
    
    if (!$row) {
      throw new \exception('invalid id ' . $id . ' for entity ' . $entity);
    }
    
    return $row;
  }
  
  public function idExists($entity, $id)
  {
    $q = $this->pdo->prepare("
      SELECT count(*) FROM `$entity` where `id` = :id
    ");
    $q->execute(['id' => $id]);
    
    $count = $q->fetchColumn();
    
    return $count > 0;
  }
  
  public function write($entity, $id, $row, $allowOverwrite)
  {
    // clone and sanitize the row
    $row = clone (object)$row;
    $row->id = $id;
    
    $structure = $this->entityStructure($entity);
    
    $valuesSql = "";
    $values = ['id' => $id];
    foreach ($structure as $field) {
      $fieldName = $field->name;
      
      if ($fieldName == 'id') {
        continue;
      }
      
      if (!property_exists($row, $fieldName)) {
        continue;
      }
      
      switch ($field->type) {
        case 'string':
          if ($row->$fieldName === null || $row->$fieldName === false) {
            $value = null;
          } else {
            try {
              $value = (string)$row->$fieldName;
            } catch (\exception $e) {
              throw new \exception("Unable to convert $fieldName value into a string");
            }
          }
          
          if (strlen($value) > $field->length) {
            throw new \exception('Value too long for table column ' . $fieldName);
          }
          break;
        case 'date':
          if ($row->$fieldName === null || $row->$fieldName === false) {
            $value = null;
          } else if (is_a($row->$fieldName, 'DateTime')) {
            $value = $row->$fieldName->format('Y-m-d');
          } else {
            throw new \exception('Expecting a DateTime, but got ' . gettype($row->$fieldName) . ' for ' . $fieldName);
          }
          break;
        case 'datetime':
          if ($row->$fieldName === null || $row->$fieldName === false) {
            $value = null;
          } else if (is_a($row->$fieldName, 'DateTime')) {
            $value = $row->$fieldName->format('Y-m-d H:i:s');
          } else {
            throw new \exception('Expecting a DateTime, but got ' . gettype($row->$fieldName) . ' for ' . $fieldName);
          }
          break;
        
        case 'text':
          if ($row->$fieldName === null || $row->$fieldName === false) {
            $value = null;
          } else {
            try {
              $value = (string)$row->$fieldName;
            } catch (\exception $e) {
              throw new \exception("Unable to convert $fieldName value into a string");
            }
          }
          
          if (strlen($value) > 65535) {
            throw new \exception('Value too long for table column ' . $fieldName);
          }
          break;
        
        case 'int':
          $value = (int)$row->$fieldName;
          break;
        
        case 'uint':
          $value = (int)$row->$fieldName;
          if ($value < 0) {
            throw new exception('Cannot write negative value for table column ' . $fieldName);
          }
          break;
        
        case 'bool':
          $value = (bool)$row->$fieldName;
          break;
        
        case 'blob':
          if ($row->$fieldName === null || $row->$fieldName === false) {
            $value = null;
          } else {
            try {
              $value = (string)$row->$fieldName;
            } catch (\exception $e) {
              throw new \exception("Unable to convert $fieldName value into a string");
            }
          }
          
          if (strlen($value) > 65535) {
            throw new \exception('Value too long for table column ' . $fieldName);
          }
          break;
        
        default:
          throw new \exception('Unknown type ' . $field->type);
      }
      
      if ($value === null && !$field->allow_null) {
        throw new \exception('Attempt to write null to ' . $fieldName);
      }
      
      $values[$fieldName] = $value;
      $valuesSql .= ",\n`$fieldName` = :$fieldName";
    }
    $valuesSql = ltrim($valuesSql, ",\n");
    
    $sql = "INSERT INTO `$entity` set\n`id` = :id,\n" . $valuesSql;
    
    if ($allowOverwrite) {
      $sql .= "\n\nON DUPLICATE KEY UPDATE\n" . $valuesSql;
    }
    
    $q = $this->pdo->prepare($sql);
    
    $q->execute($values);
  }
  
  public function archive($entity, $id)
  {
    // road row
    $q = $this->pdo->prepare("
      SELECT * FROM `$entity` where `id` = :id
    ");
    $q->execute(['id' => $id]);
    $row = $q->fetch(\PDO::FETCH_OBJ);
    
    // write archive
    $q = $this->pdo->prepare("
      INSERT INTO `_archive` set
      `datetime` = :datetime,
      `entity` = :entity,
      `data` = :row
    ");
    
    $q->execute([
      'datetime' => date('Y-m-d H:i:s'),
      'entity' => $entity,
      'row' => json_encode($row, JSON_PRETTY_PRINT)
    ]);
  }
  
  public function purge($entity, $id)
  {
    $sql = "DELETE FROM `$entity` where\n`id` = :id";
    
    $q = $this->pdo->prepare($sql);
    
    $q->execute(['id' => $id]);
  }
  
  public function entityStructure($entity)
  {
    if (isset($this->structures[$entity])) {
      return $this->structures[$entity];
    }
    
    $q = $this->pdo->prepare("
      DESCRIBE `$entity`
    ");
    $q->execute();
    
    $structure = [];
    while ($rawField = $q->fetch(\PDO::FETCH_OBJ)) {
      $field = (object)[
        'name' => $rawField->Field,
        'raw_type' => $rawField->Type,
        'allow_null' => $rawField->Null == 'YES',
        'default' => $rawField->Default
      ];
      
      if (preg_match('/^varchar\(([0-9]+)\)/', $field->raw_type, $matches)) {
        $field->type = 'string';
        $field->length = (int)$matches[1];
      } else if ($field->raw_type == 'date') {
        $field->type = 'date';
      } else if ($field->raw_type == 'datetime') {
        $field->type = 'datetime';
      } else if ($field->raw_type == 'text') {
        $field->type = 'text';
      } else if ($field->raw_type == 'tinyint(1)') {
        $field->type = 'bool';
      } else if ($field->raw_type == 'int(11) unsigned') {
        $field->type = 'uint';
      } else if ($field->raw_type == 'int(11)') {
        $field->type = 'int';
      } else if ($field->raw_type == 'blob') {
        $field->type = 'blob';
      } else {
        throw new \exception("Unknown field type: " . json_encode($rawField));
      }
      
      $structure[$field->name] = $field;
    }
    
    $this->structures[$entity] = $structure;
    
    return $structure;
  }
  
  public function blankEntity($entity)
  {
  	$structure = $this->entityStructure($entity);
  	
  	$row = (object)[];
    foreach ($structure as $field) {
      $fieldName = $field->name;
      
      $row->$fieldName = $field->default;
    }
    
    return $row;
  }
  
  public function createEntity($entity)
  {
    $q = $this->pdo->prepare("
      CREATE TABLE `$entity` (
        `id` varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
    
    $q->execute();
  }
  
  public function removeEntity($entity)
  {
    $q = $this->pdo->prepare("
      DROP TABLE `$entity`;
    ");
    $q->execute();
  }
  
  public function addColumn($entity, $name, array $options=['type'=>'string', 'length'=>255, 'allow_null'=>false, 'default'=>''])
  {
    if (!isset($options['type'])) {
      $options['type'] = 'string';
    }
    if (!isset($options['length'])) {
      $options['length'] = 255;
    }
    if (!isset($options['allow_null'])) {
      $options['allow_null'] = false;
    }
    if (!isset($options['default'])) {
      $options['default'] = $options['allow_null'] ? null : '';
    }
    
    switch ($options['type']) {
      case 'string':
        $typeSql = 'varchar(' . $options['length'] . ')';
        break;
      case 'date':
        $typeSql = 'date';
        break;
      case 'datetime':
        $typeSql = 'datetime';
        break;
      case 'text':
        $typeSql = 'text';
        break;
      case 'bool':
        $typeSql = 'tinyint(1)';
        $options['allow_null'] = false;
        break;
      case 'int':
        $typeSql = 'int(11)';
        break;
      case 'blob':
        $typeSql = 'blob';
        break;
      case 'uint':
        $typeSql = 'int(11) unsigned';
        break;
      default:
        throw new \exception('Invalid type ' . $options['type']);
    }
    
    $nullSql = $options['allow_null'] ? '' : 'NOT NULL';
    
    
    $defaultSql = $options['default'] == null ? '' : 'DEFAULT :default';
    
    $q = $this->pdo->prepare("
      ALTER TABLE `$entity` ADD `$name` $typeSql $nullSql $defaultSql;
    ");
    
    $q->execute([
      'default' => $options['default']
    ]);
  }
  
  public function addIndex($entity, $columns, $unique)
  {
    $columnsSql = '';
    foreach ($columns as $column) {
      if ($columnsSql != '') {
        $columnsSql .= ', ';
      }
      
      $columnsSql .= '`' . $column . '`';
    }
    
    $uniqueSql = $unique ? 'unique' : '';
    
    dp("alter table `$entity` add $uniqueSql index ($columnsSql)");
    
    $q = $this->pdo->prepare("
      alter table `$entity` add $uniqueSql index ($columnsSql)
    ");
  }
}
