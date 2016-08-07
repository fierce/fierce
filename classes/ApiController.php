<?php

namespace Fierce;

class ApiController extends PageController
{
  public $apiParams = null; // will be read from $_POST or provided by tests
  public $rethrowExceptions = true; // tests will set this to true, making sure exceptions and errors are thrown instead of being caught and returned as an API error
  
  static public function createForCurrentRequest($page=false, $db=false, $options=[])
  {
    $controller = parent::createForCurrentRequest($page, $db, $options);
    
    $json = file_get_contents('php://input');
    
    if ($json == '') {
      $controller->apiParams = (object)[];
    } else {
      $controller->apiParams = json_decode($json);
    }
    
    if (!$controller->apiParams) {
      $controller->errorResponse("Invalid JSON in POST data.");
    }
    
    return $controller;
  }
  
  static public function run($page=false, $db=false, $options=[])
  {
    ErrorHandler::$customHandler = function($errno, $errstr, $errfile, $errline, $errcontext, $errorName) {
      $controller = new ApiController();
      $controller->errorResponse("Unknown Error", "$errorName $errstr", "$errfile on line $errline");
    };
    
    parent::run($page, $db, $options);
  }
  
  public function actionForCurrentRequest()
  {
    if (!isset($this->apiParams->action)) {
      $this->errorResponse('Invalid API Request', 'No action provided in POST data');
    }
    
    return preg_replace('/[^a-zA-Z]/', '', $this->apiParams->action);
    $methodName = $rawAction . 'Action';
    
    if ($rawAction != $action || !method_exists($this, $methodName)) {
      $this->errorResponse('Invalid API Request', "Unrecognized action `$action`");
    }
    
    return $action;
  }
  
  public function runActionNamed($action)
  {
    try {
      $methodName = $action . 'Action';
      if (!method_exists($this, $methodName)) {
        $this->errorResponse('Invalid API Request', "Invalid API action `$action`");
      }
      
      $response = $this->$methodName($this->apiParams);
      
      if (is_array($response)) {
        $response = (object)$response;
      }
      if (!is_object($response)) {
        throw new \Exception("Invalid response from action");
      }
      
      if (!isset($response->error)) {
        $response->error = false;
      }
      
      if (!headers_sent()) {
//         header('content-type: application/json');
        header('content-type: text/plain');
      }
      print(json_encode($response, JSON_PRETTY_PRINT));
      
    } catch (\Exception $e) {
      if ($this->rethrowExceptions) {
        $this->exceptionResponse($e);
      } else {
        throw $e;
      }
    }
  }
  
  public function createApiTokenAction($params)
  {
    if (!isset($params->email)) {
      throw new \exception("Required param `email` is missing.");
    }
    if (!isset($params->password)) {
      throw new \exception("Required param `password` is missing.");
    }
    
    $token = Auth::createApiToken($params->email, $params->password);
    
    if (!$token) {
      return [
        'error' => 'Invalid Credentials'
      ];
    }
    
    return [
      'authToken' => $token
    ];
  }
  
  public function apiUser()
  {
    return Auth::userForApiToken(@$this->apiParams->authToken);
  }
  
  public function requireUserOfType($typeOrTypes)
  {
    $types = is_array($typeOrTypes) ? $typeOrTypes : [$typeOrTypes];
    $types[] = 'root'; // always valid
    
    $user = $this->apiUser();
    
    if (!in_array($user->type, $types)) {
      throw new \Exception('Access Denied');
    }
  }
  
  public function errorResponse($message, $internalError=null, $trace=null)
  {
    $response = (object)[
      'error' => $message
    ];
    
    if ($internalError) {
      $response->internalError = $internalError;
    }
    if ($trace) {
      $response->trace = $trace;
    }
    
    if (!headers_sent()) {
      header('content-type: application/json');
    }
    die(json_encode($response, JSON_PRETTY_PRINT));
  }
  
  public function exceptionResponse($exception)
  {
    $this->errorResponse('Exception Raised', $exception->getMessage(), $exception->getTraceAsString());
  }
}
