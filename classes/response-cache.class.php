<?

namespace F;

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class ResponseCache
{
  static public $willCache = false;
  
  public static function start()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
      return;
    }
    
    if (defined('F_DISABLE_CACHE') && F_DISABLE_CACHE) {
      return;
    }
    
    $cacheSeed = sha1(serialize([
      REQUEST_URL
    ]));
    
    ob_start();
    self::$willCache = true;
  }
  
  public static function saveCacheIfEnabled()
  {
    global $db;
    
    if (!self::$willCache) {
      return;
    }
    
    $url = REQUEST_URL;
    if ($url == '/') {
      $url = '/index';
    }
    
    $cacheFile = BASE_PATH . ltrim($url, '/') . '.html';
    
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
  
  public static function disable()
  {
    if (self::$willCache) {
      ob_end_flush();
    }
    self::$willCache = false;
  }
  
  public static function flushAll()
  {
    global $db;
    
    $entries = $db->cached_pages->find();
    foreach ($entries as $id => $row) {
      $cacheFile = BASE_PATH . ltrim($row->url, '/') . '.html';
      if (file_exists($cacheFile)) {
        unlink($cacheFile);
      }
      
      $db->cached_pages->purge($id);
    }
    
  }
}
