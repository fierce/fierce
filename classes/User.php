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

class User
{
  private $id;
  private $row;
  
  public $displayNames = [
    'email' => 'Email',
    'newPassword' => 'New Password'
  ];
  
  static public function all($sort=null)
  {
    $db = Env::get('db');
    
    $rows = $db->User->find([], '-modified');
    
    $users = array();
    foreach ($rows as $id => $row) {
      $user = new User();
      $user->id = $id;
      $user->setData($row);
      
      $users[] = $user;
    }
    
    return $users;
  }
  
  static public function createById($id)
  {
    $db = Env::get('db');
    
    $id = preg_replace('/[^a-z0-9-]/', '', $id);
    
    $row = $db->User->byId($id);
    
    $user = new User();
    $user->id = $id;
    $user->setData($row);
    
    return $user;
  }
  
  static public function createNew()
  {
    $db = Env::get('db');
    
    $user = new User();
    $user->id = $db->id();
    $user->setData([
      'type' => 'admin'
    ]);
    
    return $user;
  }
  
  public function __get($key)
  {
    switch ($key) {
      case 'id':
        return $this->id;
    }
    
    return $this->row->$key;
  }
  
  public function __set($key, $value)
  {
    switch ($key) {
      case 'id':
        $this->id = $value;
        return;
    }
    
    $this->row->$key = $value;
  }
  
  public function __isset($key)
  {
    switch ($key) {
      case 'id':
        return true;
    }
    
    return isset($this->row->$key);
  }
  
  public function setData($data)
  {
    if (is_array($data)) {
      $data = (object)$data;
    }
    if (!$this->row) {
      $db = Env::get('db');
      $this->row = $db->User->blankRow();
    }
    
    if (isset($data->id)) {
      $this->row->id = (string)$data->id;
    }
    if (isset($data->name)) {
      $this->row->name = (string)$data->name;
    }
    if (isset($data->type)) {
      $this->row->type = (string)$data->type;
    }
    if (isset($data->email)) {
      $this->row->email = (string)@$data->email;
    }
    if (isset($data->password)) {
      $this->row->password = (string)@$data->password;
    }
    if (isset($data->newPassword)) {
      $this->row->newPassword = (string)@$data->newPassword;
    }
  }
  
  public function save($checkLogin=true)
  {
    $db = Env::get('db');
    
    if ($checkLogin) {
      Auth::requireAdmin();
      
      $user = Auth::loggedInUser();
      $isCurrentUser = $user && $user->id == $this->id;
    } else {
      $user = null;
      $isCurrentUser = false;
    }
    
    
    
    // apply new id (email may have changed)
    $oldId = $this->id;
    $this->id = sha1(strtolower($this->row->email) . Env::get('auth_salt'));
    $this->row->id = $this->id;
    
    // misc fields
    $this->row->modified = new \DateTime();
    if ($user) {
      $this->row->modifiedBy = $user->id;
    } else {
      $this->row->modifiedBy = 'none';
    }
    
    // reset password if it changed
    $newPassword = false;
    if (@$this->row->newPassword) {
      $this->row->password = Auth::hashForPassword($this->email, $this->newPassword);
      
      $newPassword = $this->row->newPassword;
      unset($this->row->newPassword);
    }
    if (!isset($this->password)) {
      $this->password = '';
    }
    
    // hash everything
    $this->row->signature = Auth::signatureForUser($this);
    
    $db->User->archive($oldId);
    $db->User->write($this->id, $this->row, true);
    
    if ($isCurrentUser && $newPassword) {
      Auth::attemptLogin($this->row->email, $newPassword);
    }
  }
  
  public function archive()
  {
    $db = Env::get('db');
    
    $db->User->archive($this->id);
  }
  
  public function purge()
  {
    $db = Env::get('db');

    $db->User->purge($this->id);
  }
}
