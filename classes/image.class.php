<?

namespace F;

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
    
    if (file_exists(BASE_PATH . $url)) {
      list($destWidth, $destHeight) = getimagesize(BASE_PATH . $url);
      return $url;
    }
    
    // get original width/height
    list($origWidth, $origHeight) = getimagesize(BASE_PATH . $this->url);
    
    // create destination width/height
    if ($allowCrop) {
      throw new \exception('not yet implemented');
    }
    $destWidth = ($origWidth > $origHeight) ? $w : round($origWidth * ($h / $origHeight));
    $destHeight = ($origWidth < $origHeight) ? $h : round($origHeight * ($w / $origWidth));
    
    if (!$allowScaleUp) {
      if ($destWidth >= $origWidth || $destHeight >= $origHeight) {
        list($destWidth, $destHeight) = getimagesize(BASE_PATH . $this->url);
        return $this->url;
      }
    }
    
    $thumbImage = imagecreatetruecolor($destWidth, $destHeight);
    
    switch (pathinfo($this->url, PATHINFO_EXTENSION)) {
      case 'jpg':
        $sourceImage = imagecreatefromjpeg(BASE_PATH . $this->url);
        break;
      case 'png':
        $sourceImage = imagecreatefrompng(BASE_PATH . $this->url);
        break;
      case 'gif':
        $sourceImage = imagecreatefromgif(BASE_PATH . $this->url);
        break;
      default:
        throw new \exception('invalid type');
    }
    
    imagealphablending($thumbImage, false);
    imagesavealpha($thumbImage, true);
    imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $destWidth, $destHeight, $origWidth, $origHeight);
    
    if (!is_dir(dirname(BASE_PATH . $url))) {
      mkdir(dirname(BASE_PATH . $url), 0777, true);
    }
    
    switch (pathinfo($url, PATHINFO_EXTENSION)) {
      case 'jpg':
        imagejpeg($thumbImage, BASE_PATH . $url, 80);
        break;
      case 'png':
        imagepng($thumbImage, BASE_PATH . $url, 9);
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