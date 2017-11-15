<?php

namespace Fierce;

class FilesystemNode
{
  public $path;
  
  public function __construct($path)
  {
    // if $path exists, clean it up
    $this->path = realpath($path) !== false ? realpath($path) : $path;
  }
  
  public function child($childPath)
  {
    return new FilesystemNode($this->path . '/' . $childPath);
  }
  
  public function parent()
  {
    return new FilesystemNode(dirname($this->path));
  }
  
  public function name()
  {
    return pathinfo($this->path, PATHINFO_BASENAME);
  }
  
  /**
   * check if path exists and is a file.
   */
  public function isFile()
  {
    return is_file($this->path);
  }
  
  public function isDir()
  {
    return is_dir($this->path);
  }
  
  public function extension()
  {
    return pathinfo($this->path, PATHINFO_EXTENSION);
  }
  
  public function contents()
  {
    if (!$this->isFile()) {
      throw new \Exception("Cannot read file '$this->path'");
    }
    
    return file_get_contents($this->path);
  }
  
  public function createDir($mode = 0777)
  {
    $success = mkdir($this->path, $mode, true);
    
    if (!$success) {
    	throw new \Exceltion("Cannot create '$this->path'");
    }
  }
  
  function deleteFile()
  {
    $success = unlink($this->path);
    
    if (!$success) {
    	throw new \Exceltion("Cannot remove '$this->path'");
    }
  }
  
  public function children()
  {
    if (!$this->isDir()) {
      throw new \Exception("Cannot access children of non-directory '$this->path'");
    }
    
    $children = [];
    
    $childNames = scandir($this->path);
    foreach ($childNames as $name) {
      if ($name == '.' || $name == '..') {
        continue;
      }
      
      $children[] = $this->child($name);
    }
    
    return $children;
  }
  
  public function relativePath($relativeTo=null)
  {
    if (!$relativeTo) {
      $relativeTo = Env::get('baseDir');
    }
    if (!is_object($relativeTo)) {
      $relativeTo = new FilesystemNode($relativeTo);
    }
    
    if (substr($this->path, 0, strlen($relativeTo->path) + 1) != ($relativeTo->path . '/')) {
      return $this->path;
    }
    
    return substr($this->path, strlen($relativeTo->path) + 1);
  }
  
  public function isDescendantOf(FilesystemNode $dir)
  {
    return substr($this->path, 0, strlen($dir->path)) == $dir->path;
}
}
