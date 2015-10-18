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

class PhotoFieldNode extends FieldNode
{
  public static $tagName = 'photo_field';
  
  public function compileTag()
  {
    // apply class to the field
    $classNode = new \Twig_Node_Expression_Constant('photo_upload ', $this->lineno);
    
    if ($this->hasNode('class')) {
      $classNode = new \Twig_Node([
        $classNode,
        $this->getNode('class')
      ]);
    }
    $this->setNode('class', $classNode);
    
    // upload field id
    $this->compiler
      ->write('$context[\'_fierce_photo_field_id\'] = trim(preg_replace(\'/[^a-zA-Z0-9_]+/\', \'_\', ')
      ->subcompile($this->getNode('name'))
      ->raw("), '_') . '_field';\n")
    ;
    $uploadFieldIdNode = new \Twig_Node_Expression_Name('_fierce_photo_field_id', $this->lineno);
    
    // preview nodes
    $this->compiler
      ->write('$context[\'_fierce_photo_preview_wrapper_id\'] = trim(preg_replace(\'/[^a-zA-Z0-9_]+/\', \'_\', ')
      ->subcompile($this->getNode('name'))
      ->raw("), '_') . '_preview_wrapper';\n")
    ;
    $uploadPreviewWrapperIdNode = new \Twig_Node_Expression_Name('_fierce_photo_preview_wrapper_id', $this->lineno);
    
    $this->compiler
      ->write('$context[\'_fierce_photo_preview_id\'] = trim(preg_replace(\'/[^a-zA-Z0-9_]+/\', \'_\', ')
      ->subcompile($this->getNode('name'))
      ->raw("), '_') . '_preview';\n")
    ;
    $uploadPreviewIdNode = new \Twig_Node_Expression_Name('_fierce_photo_preview_id', $this->lineno);
    
    $uploadPreviewSrcNode = new \Twig_Node_Expression_GetAttr(
      new \Twig_Node_Expression_Name('_fierce_current_form_data', $this->lineno),
      new \Twig_Node_Expression_Binary_Concat(
        $this->getNode('name'),
        new \Twig_Node_Expression_Constant('_src', $this->lineno),
        $this->lineno
      ),
      null,
      'any',
      $this->lineno
    );
    
    
    
    // output upload field (for seleting files)
    $this->openTag('input', [
      'type' => 'file',
      'class' => $classNode,
      'name' => $this->getNode('name')
    ]);
    
    // output hidden field (for actually holding the file contents)
    $this->openTag('input', [
      'type' => 'hidden',
      'id' => $uploadFieldIdNode,
      'name' => $this->getNode('name')
    ]);
    
    // output preview
    $this->openTag('div', [
      'id' => $uploadPreviewWrapperIdNode,
      'class' => 'photo_preview_wrapper'
    ]);
    
    $this->openTag('img', [
      'id' => $uploadPreviewIdNode,
      'src' => $uploadPreviewSrcNode
    ]);
    
    
    self::closeTag('div');
    
    // add js
    $this->requireScript(\Fierce\Env::get('fierce_src') . 'scripts/photo-field.controller.js');
  }
}
