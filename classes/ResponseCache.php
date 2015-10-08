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

class ResponseCache
{
  static public $willCache = false;
  
  static public function start()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
      return;
    }
    
    $cacheSeed = sha1(serialize([
      Env::get('request_url')
    ]));
    
    ob_start();
    self::$willCache = true;
  }
  
  static public function saveCacheIfEnabled()
  {
    global $db;
    
    if (!self::$willCache) {
      return;
    }
    
    $url = Env::get('request_url');
    if ($url == '/') {
      $url = '/index';
    }
    
    $cacheFile = Env::get('base_path') . ltrim($url, '/') . '.html';
    
    $contents = ob_get_flush();
    
    if (!is_dir(dirname($cacheFile))) {
      mkdir(dirname($cacheFile), 0777, true);
    }
    
    file_put_contents($cacheFile, $contents);
    
    $db->cached_pages->write(sha1($url), (object)[
      'expires' => strtotime('30 days'),
      'url' => $url
    ], true);
  }
  
  static public function disable()
  {
    if (self::$willCache) {
      ob_end_flush();
    }
    self::$willCache = false;
  }
  
  static public function flushAll()
  {
    global $db;
    
    $entries = $db->cached_pages->find();
    foreach ($entries as $id => $row) {
      $cacheFile = Env::get('base_path') . ltrim($row->url, '/') . '.html';
      if (file_exists($cacheFile)) {
        unlink($cacheFile);
      }
      
      $db->cached_pages->purge($id);
    }
    
  }
}
