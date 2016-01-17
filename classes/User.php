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
    'new_password' => 'New Password'
  ];
  
  static public function all($sort=null)
  {
    global $db;
    
    $rows = $db->user->find([], '-modified');
    
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
    global $db;
    
    $id = preg_replace('/[^a-z0-9-]/', '', $id);
    
    $row = $db->user->byId($id);
    
    $user = new User();
    $user->id = $id;
    $user->setData($row);
    
    return $user;
  }
  
  static public function createNew()
  {
    global $db;
    
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
      $this->row = (object)[];
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
    if (isset($data->new_password)) {
      $this->row->new_password = (string)@$data->new_password;
    }
  }
  
  public function save($checkLogin=true)
  {
    global $db;
    
    $user = Auth::loggedInUser();
    $isCurrentUser = $user && $user->id == $this->id;
    
    // apply new id (email may have changed)
    $oldId = $this->id;
    $this->id = sha1(strtolower($this->row->email) . Env::get('auth_salt'));
    $this->row->id = $this->id;
    
    // misc fields
    $this->row->modified = new \DateTime();
    $this->row->modified_by = $user ? $user->id : '';
    
    // reset password if it changed
    $newPassword = false;
    if (@$this->row->new_password) {
      $hashForCookie = sha1(password_hash($this->row->new_password, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $this->id]));
      $hashForDatabase = sha1(password_hash($hashForCookie, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $this->id]));	
      
      $this->row->password = $hashForDatabase;
      
      $newPassword = $this->row->new_password;
      unset($this->row->new_password);
    }
    if (!isset($this->password)) {
      $this->password = '';
    }
    
    // hash everything
    $this->row->signature = sha1($this->row->id . $this->row->type . $this->row->email . $this->row->password . Env::get('auth_salt'));
    
    $db->user->archive($oldId);
    $db->user->write($this->id, $this->row, true);
    
    if ($isCurrentUser && $newPassword) {
      Auth::attemptLogin($this->row->email, $newPassword);
    }
  }
  
  public function archive()
  {
    global $db;
    
    $db->user->archive($this->id);
  }
}
