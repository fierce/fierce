<?php

namespace Fierce;

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class FormData
{
  public $_fields = [];
  
  public function __construct($fields)
  {
    foreach ($fields as $field) {
      $this->addField($field);
    }
  }
  
  public function addField($field)
  {
    if (is_string($field)) {
      $field = (object)[
        'name' => $field
      ];
    } else if (is_array($field)) {
      $field = (object)$field;
    }
    if (!isset($field->name)) {
      throw new \exception('field must have a name');
    }
    
    if (!isset($field->displayName)) {
      $displayName = $field->name;
      $displayName = str_replace('_', ' ', $displayName);
      $displayName = ucwords($displayName);
      $field->displayName = $displayName;
    }
    
    if (!isset($field->required)) {
      $field->required = true;
    }
    
    if (!isset($field->value)) {
      $field->value = null;
    }
    
    $this->_fields[$field->name] = $field;
  }
  
  public function retrieve()
  {
    foreach ($this->_fields as $field) {
      if (isset($_POST[$field->name])) {
        $field->value = $_POST[$field->name];
        continue;
      }
      if (isset($_GET[$field->name])) {
        $field->value = $_GET[$field->name];
        continue;
      }
      $field->value = null;
    }
  }
  
  public function setValues($data)
  {
    if (is_array($data)) {
      $data = (object)$data;
    }
    foreach ($this->_fields as $field) {
      $fieldName = $field->name;
      if (!isset($data->$fieldName)) {
        continue;
      }
      
      $field->value = $data->$fieldName;
    }
  }
  
  public function __get($key)
  {
    if (!isset($this->_fields[$key])) {
      throw new \Exception("invalid key: '$key'");
    }
    
    return $this->_fields[$key]->value;
  }
  
  public function __set($key, $value)
  {
    if (!isset($this->_fields[$key])) {
      throw new \Exception("invalid key: '$key'");
    }
    
    $this->_fields[$key]->value = $value;
  }
  
  public function __isset($key)
  {
    return isset($this->_fields[$key]);
  }
}
