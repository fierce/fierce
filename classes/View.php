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
  
  public static $twig = false;
  
  static protected function initTwig()
  {
    if (self::$twig) {
      return;
    }
    
    $loader = new \Twig_Loader_Filesystem([
      Env::get('base_path') . 'views/',
      Env::get('fierce_path') . 'views/'
    ]);
    
    self::$twig = new \Twig_Environment($loader, [
      'strict_variables' => true
    ]);
    
    self::$twig->addTokenParser(new Tag\NavParser());
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\IncludeCssNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\IncludeScriptNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\FieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\FieldRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\SelectNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\SelectRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\TextareaNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\TextareaRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\WysiwygNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\WysiwygRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\TagFieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\TagFieldRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\PhotoFieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\PhotoFieldRowNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\DateFieldNode'));
    self::$twig->addTokenParser(new Tag\Parser('Fierce\\Tag\\DateFieldRowNode'));
    self::$twig->addTokenParser(new Tag\FormParser());
    
    self::$twig->addFilter(new \Twig_SimpleFilter('ltrim', 'ltrim'));
    self::$twig->addFilter(new \Twig_SimpleFilter('dp', 'dp'));
  }
  
  static public function main($templateView, $contentView = false, $vars = array())
  {
    self::initTwig();
    
    $twigVars = array_merge(
      get_defined_constants(),
      Env::$vars,
      [
        'loggedInUser' => Auth::loggedInUser(),
        'authHaveRoot' => Auth::haveRoot(),
        'cssUrls' => &self::$cssUrls,
        'scriptUrls' => &self::$scriptUrls,
        'Embed' => new EmbedRenderer()
      ],
      self::$vars,
      $vars
    );
    
    if ($contentView) {
      $twigVars['contentViewHtml'] = self::$twig->render($contentView, $twigVars);
    } else if (isset($vars['contentViewTpl'])) {
      
      $twigVars['contentViewHtml'] = self::$twig->createTemplate($vars['contentViewTpl'])->render($twigVars);
      unset($vars['contentViewTpl']);
      
    } else if (!isset($vars['contentViewHtml'])) {
      $twigVars['contentViewHtml'] = false;
    }
    
    print self::$twig->render($templateView, $twigVars);
  }
  
  static public function renderTpl($contentView, $vars = array())
  {
    self::initTwig();
    
    $twigVars = array_merge(
      get_defined_constants(),
      Env::$vars,
      [
        'loggedInUser' => Auth::loggedInUser(),
        'authHaveRoot' => Auth::haveRoot(),
        'cssUrls' => &self::$cssUrls,
        'scriptUrls' => &self::$scriptUrls,
        'Embed' => new EmbedRenderer()
      ],
      self::$vars,
      $vars
    );
    

    print self::$twig->render($contentView, $twigVars);
  }
  
  static public function renderString($templateString, $vars = array())
  {
    self::initTwig();
    
    $twigVars = array_merge(
      get_defined_constants(),
      Env::$vars,
      [
        'loggedInUser' => Auth::loggedInUser(),
        'authHaveRoot' => Auth::haveRoot(),
        'cssUrls' => &self::$cssUrls,
        'scriptUrls' => &self::$scriptUrls,
        'Embed' => new EmbedRenderer()
      ],
      self::$vars,
      $vars
    );
    
    print self::$twig->createTemplate($templateString)->render($twigVars);
  }
  
  static public function set($key, $value)
  {
    self::$vars[$key] = $value;
  }
  
  static public function get($key)
  {
    return @self::$vars[$key];
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
    if ($thumb2xWidth > $thumbWidth && pathinfo(Env::get('base_path') . $imageUrl, PATHINFO_EXTENSION) != 'svg') {
      $srcsetHtml = "srcset=\"$thumbUrl 1x,$thumb2xUrl 2x\"";
    }
    
    return "<img src=\"$thumbUrl\" width=\"$thumbWidth\" height=\"$thumbHeight\"$srcsetHtml>";
  }
}
