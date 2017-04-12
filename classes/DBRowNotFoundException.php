<?php

namespace Fierce;

class DBRowNotFoundException extends \Exception
{
  /**
   * Creating new exceptions is slow, so a shared instance is available for use when it's unnecessary to know which
   * row could not be found - often this is obvious from the URL or similar.
   */
  public function sharedInstance()
  {
    static $shared = null;
    
    if (!$shared) {
      $shared = new DBRowNotFoundException("Row not found");
    }
    
    return $shared;
  }
}
