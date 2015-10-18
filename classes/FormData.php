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
  
  public function retrieve()
  {
    foreach ($this->_fields as $field) {
      if (isset($_POST[$field->name])) {
        $rawValue = $_POST[$field->name];
      } else if (isset($_GET[$field->name])) {
        $rawValue = $_GET[$field->name];
      } else {
        $rawValue = null;
      }
      
      
      switch ($field->type) {
        case 'string':
          $field->value = $rawValue;
          break;
        case 'date':
          if (!$rawValue) {
            $field->value = null;
            break;
          }
          
          $dieFunction = function() {
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
          switch ($month) {
            case 'Jan':
            case 'January':
              $month = 1;
              break;
            case 'Feb':
            case 'February':
              $month = 2;
              break;
            case 'Mar':
            case 'March':
              $month = 3;
              break;
            case 'Apr':
            case 'April':
              $month = 4;
              break;
            case 'May':
              $month = 5;
              break;
            case 'Jun':
            case 'June':
              $month = 6;
              break;
            case 'Jul':
            case 'July':
              $month = 7;
              break;
            case 'Aug':
            case 'August':
              $month = 8;
              break;
            case 'Sep':
            case 'September':
              $month = 9;
              break;
            case 'Oct':
            case 'October':
              $month = 10;
              break;
            case 'Nov':
            case 'November':
              $month = 11;
              break;
            case 'Dec':
            case 'December':
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
          $date = new \DateTimeImmutable($dateStr);
          
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
}
