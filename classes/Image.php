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

class Image
{
  public function __construct($url)
  {
    $this->url = $url;
  }
  
  public function createThumbnail($w, $h, $allowCrop, $allowScaleUp, &$destWidth, &$destHeight)
  {
    if (pathinfo($this->url, PATHINFO_EXTENSION) == 'svg') {
      $destWidth = $w;
      $destHeight = $h;
      return $this->url;
    }
    
    
    $url = $this->thumbnailUrl($w, $h, $allowCrop);
    
    if (file_exists(Env::get('base_path') . $url)) {
      list($destWidth, $destHeight) = getimagesize(Env::get('base_path') . $url);
      return $url;
    }
    
    // get original width/height
    list($origWidth, $origHeight) = getimagesize(Env::get('base_path') . $this->url);
    
    // create destination width/height
    if ($allowCrop) {
      throw new \exception('not yet implemented');
    }
    $destWidth = ($origWidth > $origHeight) ? $w : round($origWidth * ($h / $origHeight));
    $destHeight = ($origWidth < $origHeight) ? $h : round($origHeight * ($w / $origWidth));
    
    if (!$allowScaleUp) {
      if ($destWidth >= $origWidth || $destHeight >= $origHeight) {
        list($destWidth, $destHeight) = getimagesize(Env::get('base_path') . $this->url);
        return $this->url;
      }
    }
    
    $thumbImage = imagecreatetruecolor($destWidth, $destHeight);
    
    switch (pathinfo($this->url, PATHINFO_EXTENSION)) {
      case 'jpg':
        $sourceImage = imagecreatefromjpeg(Env::get('base_path') . $this->url);
        break;
      case 'png':
        $sourceImage = imagecreatefrompng(Env::get('base_path') . $this->url);
        break;
      case 'gif':
        $sourceImage = imagecreatefromgif(Env::get('base_path') . $this->url);
        break;
      default:
        throw new \exception('invalid type');
    }
    
    imagealphablending($thumbImage, false);
    imagesavealpha($thumbImage, true);
    imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $destWidth, $destHeight, $origWidth, $origHeight);
    
    if (!is_dir(dirname(Env::get('base_path') . $url))) {
      mkdir(dirname(Env::get('base_path') . $url), 0777, true);
    }
    
    switch (pathinfo($url, PATHINFO_EXTENSION)) {
      case 'jpg':
        imagejpeg($thumbImage, Env::get('base_path') . $url, 80);
        break;
      case 'png':
        imagepng($thumbImage, Env::get('base_path') . $url, 9);
        break;
      default:
        throw new \exception('invalid type');
    }
    
    return $url;
  }
  
  protected function thumbnailUrl($w, $h, $allowCrop)
  {
    switch (pathinfo($this->url, PATHINFO_EXTENSION)) {
      case 'jpg':
        $thumbExt = 'jpg';
        break;
      case 'png':
      case 'gif':
        $thumbExt = 'png';
        break;
      case 'svg':
        return $this->url;
    }
    $thumbUrl = 'images/thumbs/' . pathinfo($this->url, PATHINFO_FILENAME) . "-$w-$h" . ($allowCrop ? '-c' : '') . ".$thumbExt";
    
    return $thumbUrl;
  }
}