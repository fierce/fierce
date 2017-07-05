<?php

namespace Fierce;

/**
 * This class is similar to PHP's ZipArchive class, except it uses /usr/bin/zip for significantly improved
 * performance and memory requirements.
 */

class ZipFile
{
  public $path;
  
  public function __construct($path)
  {
    if ($path[0] != '/') {
      throw new \Exception('must be an absolute path');
    }
    $this->path = $path;
  }
  
  public function addFromString($path, $contents)
  {
    // create a tmp dir
    $tmp = self::createTmpDir();
    $oldCwd = getcwd();
    chdir($tmp);
    
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
    
    // add tmp file to the zip
    $zipPath = escapeshellarg($this->path);
    $filePath = escapeshellarg($path);
    $error = shell_exec("/usr/bin/zip -q $zipPath $filePath 2>&1");
    
    if ($error != '') {
      throw new \Exception("error adding to zip: $error");
    }
    
    // clean up
    shell_exec('rm -r ' . escapeshellarg($tmp));
    chdir($oldCwd);
  }
  
  public function addFile($sourceFile, $path)
  {
    if ($sourceFile[0] != '/') {
      throw new \Exception('$sourceFile must be absolute path');
    }
    
    // create a tmp dir, and write contents to it
    $tmp = self::createTmpDir();
    $oldCwd = getcwd();
    chdir($tmp);
    
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
    
    // add tmp file to the zip
    $zipPath = escapeshellarg($this->path);
    $filePath = escapeshellarg($path);
    $error = shell_exec("/usr/bin/zip -q $zipPath $filePath 2>&1");
    
    if ($error != '') {
      throw new \Exception("error adding to zip: $error");
    }
    
    // clean up
    shell_exec('rm -r ' . escapeshellarg($tmp));
    chdir($oldCwd);
  }
  
  private function createTmpDir()
  {
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
    
    return $tmpDir;
  }
}
