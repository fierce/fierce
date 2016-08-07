<?php

namespace Fierce;

class Mock
{
  private $className;
  private $preparedCalls = [];
  private $testController;
  private $displayName;
  private $mockVars = [];
  
  public function __construct($className, $testController, $displayName=null)
  {
    $this->className = $className;
    $this->testController = $testController;
    $this->displayName = $displayName;
  }
  
  // return can be a value or closure (which will be executed with the actual arguments)
  public function prepareForCall($methodName, $args=null, $return=null)
  {
    if (!isset($this->preparedCalls[$methodName])) {
      $this->preparedCalls[$methodName] = [];
    }
    
    foreach ($this->preparedCalls[$methodName] as $preparedCallIndex => $preparedCall) {
      if ($preparedCall->args === $args) {
        unset($this->preparedCalls[$methodName][$preparedCallIndex]);
      }
    }
    
    $this->preparedCalls[$methodName][] = (object)[
      'args' => $args,
      'return' => $return
    ];
  }
  
  public function __get($varName)
  {
    if (!isset($this->mockVars[$varName])) {
      $this->testController->fail("Unexpected attempt to access Mocked <pre>$this->className-&gt;$varName</pre>" . ($this->displayName ? " ($this->displayName)" : ''), true);
      return;
    }
    
    return $this->mockVars[$varName];
  }
  
  public function __set($varName, $value)
  {
    $this->mockVars[$varName] = $value;
  }
  
  public function __call($methodName, $args)
  {
    if (!isset($this->preparedCalls[$methodName])) {
      $this->preparedCalls[$methodName] = [];
    }
    
    // search for prepared call with these args
    $preparedCall = null;
    foreach ($this->preparedCalls[$methodName] as $preparedCallIndex => $possiblePreparedCall) {
      if ($possiblePreparedCall->args !== $args) {
        continue;
      }
      
      $preparedCall = $possiblePreparedCall;
      break;
    }
    
    // search for prepared call with no args
    if (!$preparedCall) {
      foreach ($this->preparedCalls[$methodName] as $preparedCallIndex => $possiblePreparedCall) {
        if ($possiblePreparedCall->args !== null) {
          continue;
        }
        
        $preparedCall = $possiblePreparedCall;
        break;
      }
    }
    
    if (!$preparedCall) {
      ob_start();
      var_dump($args);
      $argsDump = htmlspecialchars(trim(ob_get_clean()));
      
      $this->testController->fail("Unexpected attempt to call <pre>$methodName($argsDump)</pre> on Mocked <pre>$this->className</pre>" . ($this->displayName ? " ($this->displayName)" : ''), true);
      return;
    }
    
    if (is_object($preparedCall->return) && $preparedCall->return instanceof \Closure) {
      return call_user_func_array($preparedCall->return, $args);
    }
    
    return $preparedCall->return;
  }
}
