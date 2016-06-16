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
  
  public function __construct($fields=[])
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
    if (!isset($field->type)) {
      $field->type = 'string';
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
  
  public function getFields()
  {
    return $this->_fields;
  }
  
  public function retrieve()
  {
    $this->fromStringArray(array_merge($_GET, $_POST));
  }
  
  public function fromStringArray($values)
  {
    foreach ($this->_fields as $field) {
      if (isset($values[$field->name])) {
        $rawValue = $values[$field->name];
      } else {
        $rawValue = null;
      }
      
      switch ($field->type) {
        
        
        case 'string':
          $field->value = $rawValue;
          break;
        case 'email':
          $field->value = trim($rawValue);
          break;
        case 'date':
          if (!$rawValue) {
            $field->value = null;
            break;
          }
          
          $dieFunction = function() use ($field) {
            die('
              <p>Invalid value provided for ' . $field->displayName . ' (must be: Day Month Year).</p>
              <p><a href="javascript:window.history.back()">Go Back</a></p>
            ');
          };
          
          $dateComponents = preg_split('/[^a-zA-Z0-9]+/', trim($rawValue));
          if (count($dateComponents) != 3) {
            $dieFunction();
          }
          
          list($day, $month, $year) = $dateComponents;
          
          $year = (int)$year;
          if ($year < 1000 || $year > 3000) {
            $dieFunction();
          }
          switch (strtolower($month)) {
            case 'jan':
            case 'january':
              $month = 1;
              break;
            case 'feb':
            case 'february':
              $month = 2;
              break;
            case 'mar':
            case 'march':
              $month = 3;
              break;
            case 'apr':
            case 'april':
              $month = 4;
              break;
            case 'may':
              $month = 5;
              break;
            case 'jun':
            case 'june':
              $month = 6;
              break;
            case 'jul':
            case 'july':
              $month = 7;
              break;
            case 'aug':
            case 'august':
              $month = 8;
              break;
            case 'sep':
            case 'september':
              $month = 9;
              break;
            case 'oct':
            case 'october':
              $month = 10;
              break;
            case 'nov':
            case 'november':
              $month = 11;
              break;
            case 'dec':
            case 'december':
              $month = 12;
              break;
          }
          $month = (int)$month;
          if ($month < 1 || $month > 12) {
            $dieFunction();
          }
          
          $day = (int)$day;
          if ($day < 1 || $day > 31) {
            $dieFunction();
          }
          
          $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
          $date = new \DateTime($dateStr);
          
          if ($date->format('Y-m-d') != $dateStr) { // eg febuary 31st will fail here
            $dieFunction();
          }
          
          $field->value = $date;
          break;
        default:
          throw new \exception('unknown type ' . $field->type);
      }
    }
  }
  
  public function toStringArray()
  {
    $values = [];
    foreach ($this->_fields as $field) {
      switch ($field->type) {
        case 'string':
          $value = $field->value;
          break;
        case 'email':
          $value = $field->value;
          break;
        case 'date':
          if (!$field->value) {
            $value = null;
            break;
          }
          
          $value = $field->value->format('j M Y');
          break;
        default:
          throw new \exception('unknown type ' . $field->type);
      }
      
      $values[$field->name] = $value;
    }
    
    return $values;
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
  
  public function getValues()
  {
    $values = array();
    foreach ($this->_fields as $field) {
      $values[$field->name] = $field->value;
    }
    
    return $values;
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
  
  /**
   *
   *	@param $failureFunc - function with one argument (array of error message strings)
   *
   */
  public function validate($failureFunc)
  {
    $errors = [];
    foreach ($this->_fields as $field) {
      if ($field->required) {
        if ($field->value === null || $field->value === '') {
          $errors[] = "$field->displayName is missing.";
          continue;
        }
      }
      
      switch ($field->type) {
        case 'string':
          break;
        case 'email':
          if (!filter_var($field->value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "$field->displayName is invalid.";
          }
          break;
        case 'date':
          break;
        default:
          throw new \Exception('not sure how to handle ' . $field->name);
      }
    }
    
    if (count($errors) > 0) {
      $failureFunc($errors);
    }
  }
  
  public function hasField($field)
  {
    return isset($this->_fields[$field]);
  }
}
