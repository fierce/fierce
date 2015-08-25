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

class Page extends DBRow
{
  public function __get($key)
  {
    switch ($key) {
      case 'admin_category';
        if (@$this->nav_position) {
          return 'main';
        }
        if (@$this->is_user_page) {
          return 'not_linked';
        }
        return 'system';
      default:
        return parent::__get($key);
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
    
    $this->id = sha1($this->url);
    $this->row->id = $this->id;
    
    $db->$entity->write($this->id, $this->row);
    
  }
}
