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

namespace Fierce\Tag;

class Node extends \Twig_Node
{
  public static $keyValuePairs = true;
  public static $singleValue = false;
  
  protected $compiler;
  
  public function compileTag()
  {
    // implemented by a subclass
  }
  
  public function compile(\Twig_Compiler $compiler)
  {
    $this->compiler = $compiler;
    
    $this->compiler->addDebugInfo($this);
    $this->compileTag();
    
    unset($this->compiler);
  }
  
  public function openTag($name, $params=[], $trailingNewline=true)
  {
    $this->compiler
      ->write("print '<" . $name . "';\n")
    ;
    foreach ($params as $name => $value) {
      $this->compiler
        ->write("print ' $name=\"'")
      ;
      if (is_a($value, 'Twig_Node')) {
        $valueEscapedNode = new \Twig_Node_Expression_Filter(
          $value,
          new \Twig_Node_Expression_Constant('escape', $this->lineno),
          new \Twig_Node([
            new \Twig_Node_Expression_Constant('html', $this->lineno)
          ]),
          $this->lineno
        );
        
        $this->compiler
          ->raw(' . ')
          ->subcompile($valueEscapedNode)
        ;
      } else {
        $this->compiler
          ->raw(" . \"" . addslashes(htmlspecialchars((string)$value)) . "\"")
        ;
      }
      $this->compiler
        ->raw(" . '\"';\n")
      ;
    }
    $this->compiler
      ->write("print \">" . ($trailingNewline ? "\\n" : '') . "\";\n")
    ;
  }
  
  public function closeTag($name, $trailingNewline=true)
  {
    $this->compiler
      ->write("print \"</" . $name . ">" . ($trailingNewline ? "\\n" : '') . "\";\n")
    ;
  }
  
  /**
   * @param $contents string or node or array of strings/nodes
   */
  public function text($contents)
  {
    if (!is_array($contents)) {
      $contents = [$contents];
    }
    if (count($contents) == 0) {
      return;
    }
    
    $this->compiler
      ->write("print ")
    ;
    
    $isFirst = true;
    foreach ($contents as $content) {
      if ($isFirst) {
        $isFirst = false;
      } else {
        $this->compiler
          ->raw(" . ")
        ;
      }
      if (is_string($content)) {
        $this->compiler
          ->raw("\"" . addslashes(htmlspecialchars($content)) . "\"")
        ;
      } else if (is_a($content, 'Twig_Node')) {
        $this->compiler
          ->subcompile(new \Twig_Node_Expression_Filter(
            $content,
            new \Twig_Node_Expression_Constant('escape', $this->lineno),
            new \Twig_Node([
              new \Twig_Node_Expression_Constant('html', $this->lineno)
            ]),
            $this->lineno
          ))
        ;
      } else {
        throw new \exception("invalid content " . print_r($content, true));
      }
    }
    
    $this->compiler
      ->raw(";\n")
    ;
  }
  
  /**
   * @param $contents string or node or array of strings/nodes
   */
  public function raw($contents)
  {
    if (!is_array($contents)) {
      $contents = [$contents];
    }
    if (count($contents) == 0) {
      return;
    }
    
    $this->compiler
      ->write("print ")
    ;
    
    $isFirst = true;
    foreach ($contents as $content) {
      if ($isFirst) {
        $isFirst = false;
      } else {
        $this->compiler
          ->raw(" . ")
        ;
      }
      if (is_string($content)) {
        $this->compiler
          ->raw("\"" . addslashes($content) . "\"")
        ;
      } else if (is_a($content, 'Twig_Node')) {
        $this->compiler
          ->subcompile($content)
        ;
      } else {
        throw new \exception("invalid content " . print_r($content, true));
      }
    }
    
    $this->compiler
      ->raw(";\n")
    ;
  }
  
  public function requireCss($cssUrl)
  {
    $this->compiler->write("\Fierce\View::addCss('" . addslashes($cssUrl) . "');");
  }
  
  public function requireScript($scriptUrl)
  {
    $this->compiler->write("\Fierce\View::addScript('" . addslashes($scriptUrl) . "');");
  }
}
