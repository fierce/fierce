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

class View
{
  protected static $tagStack = [];
  
  protected static $currentForm = null;
  
  protected static $rowStack = [];
  
  protected static $vars = [];
  
  protected static $scriptUrls = [];
  protected static $cssUrls = [];
  
  protected static $twig = false;
  
  static protected function initTwig()
  {
    if (self::$twig) {
      return;
    }
    
    $loader = new \Twig_Loader_Filesystem([
      BASE_PATH . 'views/',
      FIERCE_PATH . 'views/'
    ]);
    
    if (!F_DISABLE_CACHE) {
      $cacheDir = BASE_PATH . 'tmp/twig_cache/';
      if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
      }
    }
    
    self::$twig = new \Twig_Environment($loader, [
      'cache' => F_DISABLE_CACHE ? false : $cacheDir,
      'strict_variables' => true
    ]);
    
    self::$twig->addTokenParser(new Tag\NavParser());
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\IncludeCssNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\IncludeScriptNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\FieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\FieldRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\SelectNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\SelectRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\WysiwygNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\WysiwygRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\TagFieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\TagFieldRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\PhotoFieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\PhotoFieldRowNode'));
    self::$twig->addTokenParser(new Tag\FormParser());
    
    self::$twig->addFilter(new \Twig_SimpleFilter('ltrim', 'ltrim'));
  }
  
  static public function main($templateView, $contentView = false, $vars = array())
  {
    self::initTwig();
    
    $twigVars = array_merge(
      get_defined_constants(),
      [
        'loggedInUser' => Auth::loggedInUser(),
        'authHaveRoot' => Auth::haveRoot(),
        'cssUrls' => &self::$cssUrls,
        'scriptUrls' => &self::$scriptUrls
      ],
      self::$vars,
      $vars
    );
    

    if ($contentView) {
      $twigVars['contentViewHtml'] = self::$twig->render($contentView, $twigVars);
    } else if (!isset($vars['contentViewHtml'])) {
      $twigVars['contentViewHtml'] = false;
    }
    
    print self::$twig->render($templateView, $twigVars);
  }
  
  static public function renderTpl($contentView, $vars)
  {
    $blockedVars = ['templateView', 'contentView', 'vars', 'var', 'value'];
    foreach (self::$vars as $var => $value) {
      if (in_array($var, $blockedVars)) {
        continue;
      }
      $$var = $value;
    }
    foreach ($vars as $var => $value) {
      if (in_array($var, $blockedVars)) {
        continue;
      }
      $$var = $value;
    }
    
    $contentTpl = BASE_PATH . 'views/' . $contentView;
    if (!file_exists($contentTpl)) {
      $contentTpl = FIERCE_PATH . 'views/' . $contentView;
    }
    if (!file_exists($contentTpl)) {
      throw new \exception('Can\'t find view ' . $contentView);
    }
    
    require($contentTpl);
  }
  
  static public function set($key, $value)
  {
    self::$vars[$key] = $value;
  }
  
  static public function addScript($scriptUrl)
  {
    if (in_array($scriptUrl, self::$scriptUrls)) {
      return;
    }
    
    self::$scriptUrls[] = $scriptUrl;
  }
  
  static public function addCss($cssUrl)
  {
    if (in_array($cssUrl, self::$cssUrls)) {
      return;
    }
    
    self::$cssUrls[] = $cssUrl;
  }
  
  static public function thumbnail($imageUrl, $w, $h, $allowCrop)
  {
    $image = new Image($imageUrl);
    
    $thumbUrl = $image->createThumbnail($w, $h, $allowCrop, false, $thumbWidth, $thumbHeight);
    $thumb2xUrl = $image->createThumbnail($w * 2, $h * 2, $allowCrop, false, $thumb2xWidth, $thumb2xHeight);
    
    $srcsetHtml = '';
    if ($thumb2xWidth > $thumbWidth && pathinfo(BASE_PATH . $imageUrl, PATHINFO_EXTENSION) != 'svg') {
      $srcsetHtml = "srcset=\"$thumbUrl 1x,$thumb2xUrl 2x\"";
    }
    
    return "<img src=\"$thumbUrl\" width=\"$thumbWidth\" height=\"$thumbHeight\"$srcsetHtml>";
  }
}
