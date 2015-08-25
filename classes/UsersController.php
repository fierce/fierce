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

class UsersController extends CrudController
{
  public $entity = 'Fierce\User';
  
  public $listFields = [
    'name' => 'Name',
    'email' => 'Email'
  ];
  
  public $editTpl = 'user-edit.tpl';
  
  public function __construct()
  {
    Auth::requireRoot();
    
    parent::__construct();
  }
}
