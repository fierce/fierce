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

class DBEntity
{
  protected $connection;
  protected $entity;
  
  public function __construct($connection, $entity)
  {
    $this->connection = $connection;
    $this->entity = $entity;
    
    $this->dataEntity = preg_replace('/.*\\\\/', '', $entity);
  }
  
  public function find($params = array(), $orderBy = null, $range = null)
  {
    return $this->connection->find($this->entity, $params, $orderBy, $range);
  }
  
  /**
   * Fetch a record by it's id.
   */
  public function byId($id)
  {
    return $this->connection->byId($this->entity, $id);
  }
  
  public function byFind($params = array(), $orderBy = null)
  {
    return $this->connection->byFind($this->entity, $params, $orderBy);
  }
  
  public function idExists($id)
  {
    return $this->connection->idExists($this->entity, $id);
  }
  
  public function write($id, $row, $allowOverwrite = false)
  {
    $this->connection->write($this->entity, $id, $row, $allowOverwrite);
  }
  
  public function insert($row)
  {
    return $this->connection->insert($this->entity, $row);
  }
  
  public function update($where, $row)
  {
    $this->connection->update($this->entity, $where, $row);
  }
    
  public function archive($id)
  {
    $this->connection->archive($this->entity, $id);
  }
  
  public function purge($id)
  {
    $this->connection->purge($this->entity, $id);
  }
  
  public function blankRow()
  {
    return $this->connection->blankEntity($this->entity);
  }
}
