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

class NewsPost extends DBRow
{
  public static function tableName()
  {
    return 'news_post';
  }
  
  public function __isset($key)
  {
    switch ($key) {
      case 'permalink':
        return 'news/post?id=' . $this->id;
    }
    
    return parent::__isset($key);
  }
  
  public function __get($key)
  {
    switch ($key) {
      case 'permalink':
        return 'news/post?id=' . $this->id;
    }
    
    return parent::__get($key);
  }
}
