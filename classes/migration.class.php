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

namespace F;

class Migration
{
  function run()
  {
    throw new \exception('subclass must implement this');
  }
  
  function log($str)
  {
    print '<p>' . nl2br(htmlspecialchars($str)) . '</p>';
  }
}
