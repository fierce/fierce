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

class Page extends DBRow
{
  public function __get($key)
  {
    switch ($key) {
      default:
        return parent::__get($key);
    }
  }
  
  public function __isset($key)
  {
    return parent::__isset($key);
  }
  
  public function save()
  {
    $this->id = sha1($this->url);
    $this->row->id = $this->id;
    
    parent::save();
  }
}
