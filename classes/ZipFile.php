<?php

namespace Fierce;

/**
 * This class is similar to PHP's ZipArchive class, except it uses /usr/bin/zip for significantly improved
 * performance and memory requirements.
 */

class ZipFile
{
  public $path = null;
  public $tmpDir = null; // by default this will be /tmp/<rand>. You can provide a different path.
  
  public function __construct($path)
  {
    if ($path != null && $path[0] != '/') {
      throw new \Exception('must be an absolute path');
    }
    
    if ($path) {
      $this->path = $path;
      
      if (file_exists($this->path)) {
        throw new \Exception('Path already exists');
      }
      
      file_put_contents($this->path, '');
      
      if (!file_exists($this->path)) {
        throw new \Exception('Unable to create zip');
      }
    }
  }
  
  public function addFromString($path, $contents)
  {
    $tmp = self::createTmpDir();
    
    // create parent dir if needed
    $parentDir = dirname("$tmp/$path");
    
    if (!is_dir($parentDir)) {
      mkdir($parentDir, 0777, true);
    }
    
    // write contents to tmp
    $bytesWritten = file_put_contents("$tmp/$path", $contents);
    
    if ($bytesWritten != strlen($contents)) {
      print($bytesWritten);
      throw new \Exception("error adding to zip");
    }
  }
  
  public function addFile($sourceFile, $path)
  {
    if ($sourceFile[0] != '/') {
      throw new \Exception('$sourceFile must be absolute path');
    }
    
    // create a tmp dir, and write contents to it
    $tmp = self::createTmpDir();

    // create parent dir if needed
    $parentDir = dirname("$tmp/$path");
    
    if (!is_dir($parentDir)) {
      mkdir($parentDir, 0777, true);
    }
    
    // copy into place
    $success = copy($sourceFile, "$tmp/$path");
    
    if (!$success) {
      throw new \Exception("error adding to zip");
    }
  }
  
  public function complete($compressionLevel='-6')
  {
    if (!$this->path) {
      throw new \Exception('stream not yet implemented.'); // see https://stackoverflow.com/questions/4357073/lamp-how-to-create-zip-of-large-files-for-the-user-on-the-fly-without-disk-cp
    }
    
    
    if (file_exists($this->path)) {
      if (filesize($this->path) != 0) {
        throw new \Exception('Path already exists');
      } else {
        unlink($this->path);
      }
    }
    
    $tmp = self::createTmpDir();
    $oldCwd = getcwd();
    chdir($tmp);
    
    $zipPath = escapeshellarg($this->path);
    $error = shell_exec("/usr/bin/find . | /usr/bin/zip $compressionLevel -q -@ $zipPath 2>&1");
    
    if ($error != '') {
      throw new \Exception("error adding to zip: $error");
    }
    // clean up
    chdir($oldCwd);
    shell_exec('rm -r ' . escapeshellarg($tmp));
  }
  
  private function createTmpDir()
  {
    if ($this->tmpDir) {
      return $this->tmpDir;
    }
    
    $systemTmp = sys_get_temp_dir();
    rtrim($systemTmp, '/');
    
    $tmpDir = "$systemTmp/" . sha1(rand());
    $attempts = 100;
    while (!mkdir($tmpDir)) {
      $tmpDir = "$systemTmp/" . sha1(rand());
      
      $attempts--;
      if ($attempts == 0) {
        throw new \Exception('cannot create tmp dir');
      }
    }
    
    $this->tmpDir = $tmpDir;
    
    return $tmpDir;
  }
}
