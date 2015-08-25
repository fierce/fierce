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

namespace F;

class DB
{
  public $type;
  public $dataPath;
  public $pdo;
  
  public function __construct($type, $pathOrDsn, $username=null, $password=null)
  {
    $this->type = $type;
    
    switch ($type) {
      case 'file':
        $this->dataPath = $pathOrDsn;
        break;
//       case 'pdo':
//         $this->pdo = new PDO($pathOrDsn, $username, $passsword);
//         break;
      default:
        throw new \exception('invalid type ' . $type);
    }
  }
  
  public function __get($entity)
  {
    $entity = strtolower($entity);
    $entity = preg_replace('/^f\\\/', '', $entity);
    
    return $this->$entity = new DBEntity($this, $entity);
  }
  
  public function id()
  {
    $id = openssl_random_pseudo_bytes(16);
    $id[6] = chr(ord($id[6]) & 0x0f | 0x40); // set version to 0100
    $id[8] = chr(ord($id[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($id), 4));
    
    return $id;
  }
}
