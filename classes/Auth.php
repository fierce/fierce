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

class Auth
{
  public static $inactiveLoginTimeout = 172800; // in seconds, default 2 days
  private static $loggedInUser = null;
  
  static public function requireAdmin()
  {
    if (!self::haveAdmin()) {
      self::bounceToLogin();
    }
  }
  
  static public function haveAdmin()
  {
    if (!$user = self::loggedInUser()) {
      return false;
    }
    
    if ($user->type != 'admin' && $user->type != 'root') {
      return false;
    }
    
    return true;
  }
  
  static public function requireRoot()
  {
    if (!self::haveRoot()) {
      self::bounceToLogin();
    }
  }
  
  static public function haveRoot()
  {
    if (!$user = self::loggedInUser()) {
      return false;
    }
    
    if ($user->type != 'root') {
      return false;
    }
    
    return true;
  }
  
  static public function bounceToLogin()
  {
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
      throw new \Exception('not logged in');
    }
    HTTP::redirect('login?return='  . urlencode(Env::get('request_url')));
  }
  
  static public function checkConfig()
  {
    if (!Env::get('auth_salt')) {
      throw new \exception('Auth salt must be defined');
    }
    if (Env::get('auth_salt') == '8d6f6390017eb415bcf468a050d893628e40d12f') {
      throw new \exception('Cannot use the example auth_salt. Make your own with `random | sha1`!');
    }
  }
  
  static public function loggedInUser()
  {
    self::checkConfig();
    
    if (self::$loggedInUser) {
      return self::$loggedInUser;
    }
    
    $db = Env::get('db');
    $cookieManager = Env::get('cookie');
    
    $cookieValue = $cookieManager->get('u');
    if (!$cookieValue) {
      return null;
    }
    
    $sessionId = preg_replace('/[^a-z0-9-]/', '', $cookieValue);
    try {
      $loginSession = $db->LoginSession->byId($sessionId);
    } catch (\Exception $e) {
      self::logout();
      return null;
    }
    
    // inactive for too long? kill it
    if (($loginSession->lastActive->getTimestamp() + self::$inactiveLoginTimeout) < time()) {
      self::logout();
      return null;
    }
    
    // find the user
    $user = $db->User->byId($loginSession->userId);
    
    // verify login session (block privilege escalation by a database injection)
    if ($loginSession->hash != self::tokenHashForUser($user, $loginSession->lastActive, $sessionId)){
      self::logout();
      return null;
    }
    
    // verify user hash
    if ($user->signature != self::signatureForUser($user)) {
      self::logout();
      return null;
    }
    
    // record activity on the login session
    $loginSession->lastActive = new \DateTime();
    $loginSession->hash = self::tokenHashForUser($user, $loginSession->lastActive, $sessionId);
    $db->LoginSession->write($sessionId, $loginSession, true);
    
    // bump the expiry date on the cookie
    if (!headers_sent()) {
      $cookieManager->set('u', $sessionId, self::$inactiveLoginTimeout);
    }
    
    // and finally, log them in
    self::$loggedInUser = $user;
    return $user;
  }
  
  static public function logout()
  {
    $cookieManager = Env::get('cookie');
    $sessionId = preg_replace('/[^a-z0-9]/', '', $cookieManager->get('u'));
    
    if ($sessionId) {
      $db = Env::get('db');
      $db->LoginSession->purge($sessionId);
    }
    
    $cookieManager->clear('u');
    
    self::$loggedInUser = null;
  }
  
  static public function attemptLogin($email, $password)
  {
    self::checkConfig();
    
    $db = Env::get('db');
    $cookieManager = Env::get('cookie');
    
    // check credentials
    $id = self::userIdIfCredentialsValid($email, $password);
    if (!$id) {
      return false;
    }
    $user = $db->User->byId($id);
    
    // log the user in
    $sessionId = $db->id();
    
    $session = (object)[
      'userId' => $id,
      'lastActive' => new \DateTime()
    ];
    $session->hash = self::tokenHashForUser($user, $session->lastActive, $sessionId);
    
    $db->LoginSession->write($sessionId, $session, true);
    $cookieManager->set('u', $sessionId, self::$inactiveLoginTimeout);
    
    return true;
  }
  
  static public function createApiToken($email, $password)
  {
    self::checkConfig();
    
    $db = Env::get('db');
    
    // check credentials
    $id = self::userIdIfCredentialsValid($email, $password);
    if (!$id) {
      return false;
    }
    $user = $db->User->byId($id);
    
    // log the user in
    $tokenId = $db->id();
    
    $token = (object)[
      'userId' => $id,
      'created' => new \DateTime(),
      'lastActive' => new \DateTime()
    ];
    $token->hash = self::tokenHashForUser($user, $token->created, $tokenId);
    
    $db->ApiToken->write($tokenId, $token, true);
    
    return $tokenId;
  }
  
  static public function userForApiToken($tokenId)
  {
    $db = Env::get('db');
    
    try {
      $token = $db->ApiToken->byId($tokenId);
      
      $user = $db->User->byId($token->userId);
    } catch (\Exception $e) {
      throw new \Exception("Invalid API Token");
    }
    
    if ($user->signature != self::signatureForUser($user)) {
      throw new \Exception("Invalid signature on user `$user->email`");
    }
    if ($token->hash != self::tokenHashForUser($user, $token->created, $token->id)) {
      throw new \Exception("Invalid API Token");
    }
    
    return $user;
  }
  
  static public function userIdIfCredentialsValid($email, $password)
  {
    $db = Env::get('db');
    
    self::checkConfig();
    
    // find user
    $id = self::userIdByEmail($email);
    try {
      $user = $db->User->byId($id);
    } catch (\Exception $e) {
      return null;
    }
    
    // brute force prevention
    if (self::bruteForceDetectedForUser($id)) {
      return null;
    }
    
    // verify password
    $passwordHash = self::hashForPassword($email, $password);
    
    if ($user->password != $passwordHash) {
      $userLoginFailuresRow->failures[] = time();
      $userLoginFailuresRow->failures = json_encode($userLoginFailuresRow->failures);
      $db->LoginFailure->write($id, $userLoginFailuresRow, true);
      return null;
    }
    
    // verify user hash
    if ($user->signature != self::signatureForUser($user)) {
      $userLoginFailuresRow->failures[] = time();
      $userLoginFailuresRow->failures = json_encode($userLoginFailuresRow->failures);
      $db->LoginFailure->write($id, $userLoginFailuresRow, true);
      return null;
    }
    
    return $id;
  }
  
  static public function userIdByEmail($email)
  {
    return sha1(strtolower($email) . Env::get('auth_salt')); // todo: change this to search the database, so email can be changed without id change
  }
  
  static public function hashForPassword($email, $password)
  {
    $id = self::userIdByEmail($email);
    
    // this is cached since takes ~1 second and is usually repeated within a single request
    static $resultCache = ['key' => null];
    
    $cacheKey = $id . $password;
    if ($resultCache['key'] == $cacheKey) {
      return $resultCache['result'];
    }
    
    // for historical reasons we have to double hash... would be good to remove this in future, but complicated without resetting passwords
    $hash = sha1(password_hash($password, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));
    $hash = sha1(password_hash($hash, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));
    
    $resultCache['key'] = $cacheKey;
    $resultCache['result'] = $hash;
    
    return $hash;
  }
  
  static public function signatureForUser($user)
  {
    return sha1($user->id . $user->type . $user->email . $user->password . Env::get('auth_salt'));
  }
  
  static public function tokenHashForUser($user, $dateTime, $tokenId)
  {
    return sha1($tokenId . $user->id . $user->password . $dateTime->getTimestamp() . Env::get('auth_salt'));
  }
  
  static public function bruteForceDetectedForUser($id)
  {
    $db = Env::get('db');
    
    try {
      $userLoginFailuresRow = $db->LoginFailure->byId($id);
      $userLoginFailuresRow->failures = json_decode($userLoginFailuresRow->failures);
    } catch (\Exception $e) {
      $userLoginFailuresRow = (object)['id' => $id, 'failures' => []];
    }
    
    // drop all failures older than 20 mintues
    foreach ($userLoginFailuresRow->failures as $index => $time) {
      if ($time + 1200 < time()) {
        unset($userLoginFailuresRow->failures[$index]);
      }
    }
    $userLoginFailuresRow->failures = array_values($userLoginFailuresRow->failures);
    
    // more than 20 failures? Reject this one
    while (count($userLoginFailuresRow->failures) > 20) {
      $userLoginFailuresRow->failures[] = time();
      $userLoginFailuresRow->failures = json_encode($userLoginFailuresRow->failures);
      $db->LoginFailure->write($id, $userLoginFailuresRow, true);
      return true;
    }
    
    return false;
  }
  
    // NOTE: On PHP 5.x you will need to install https://github.com/paragonie/random_compat
  
    /**
     * Generate a password that can easily be typed by users.
     *
     * By default, this will sacrifice strength by skipping characters that can cause
     * confusion. Set $allowAmbiguous to allow these characters.
     */
    static public function generatePassword($length=12, $mixedCase=true, $numericCount=2, $symbolCount=1, $allowAmbiguous=false, $allowRepeatingCharacters=false)
    {
      // sanity check to prevent endless loop
      if ($numericCount + $symbolCount > $length) {
        throw new \Exception('generatePassword(): $numericCount + $symbolCount are too high');
      }
      
      // generate a basic password with just alphabetic characters
      $chars  = 'qwertyupasdfghjkzxcvbnm';
      if ($mixedCase) {
        $chars .= 'QWERTYUPASDFGHJKZXCVBNML';
      }
      if ($allowAmbiguous) {
        $chars .= 'iol';
        if ($mixedCase) {
          $chars .= 'IO';
        }
      }
    
      $password = '';
      foreach (range(1, $length) as $index) {
        $char = $chars[random_int(0, strlen($chars) - 1)];
        
        if (!$allowRepeatingCharacters) {
          while ($char == substr($password, -1)) {
            $char = $chars[random_int(0, strlen($chars) - 1)];
          }
        }
        
        $password .= $char;
      }
    
    
      // add numeric characters
      $takenSubstitutionIndexes = [];
      
      if ($numericCount > 0) {
        $chars = '23456789';
        if ($allowAmbiguous) {
          $chars .= '10';
        }
  
        foreach (range(1, $numericCount) as $_) {
          $index = random_int(0, strlen($password) - 1);
          while (in_array($index, $takenSubstitutionIndexes)) {
            $index = random_int(0, strlen($password) - 1);
          }
          
          $char = $chars[random_int(0, strlen($chars) - 1)];
          if (!$allowRepeatingCharacters) {
            while (substr($password, $index - 1, 1) == $char || substr($password, $index + 1, 1) == $char) {
              $char = $chars[random_int(0, strlen($chars) - 1)];
            }
          }
          
          $password[$index] = $char;
          $takenSubstitutionIndexes[] = $index;
        }
      }
    
      // add symbols
      $chars = '!@#$%&*=+?';
      if ($allowAmbiguous) {
        $chars .= '^~-_()[{]};:|\\/,.\'"`<>';
      }
    
      if ($symbolCount > 0) {
        foreach (range(1, $symbolCount) as $_) {
          $index = random_int(0, strlen($password) - 1);
          while (in_array($index, $takenSubstitutionIndexes)) {
            $index = random_int(0, strlen($password) - 1);
          }
          
          $char = $chars[random_int(0, strlen($chars) - 1)];
          if (!$allowRepeatingCharacters) {
            while (substr($password, $index - 1, 1) == $char || substr($password, $index + 1, 1) == $char) {
              $char = $chars[random_int(0, strlen($chars) - 1)];
            }
          }
          
          $password[$index] = $char;
          $takenSubstitutionIndexes[] = $index;
        }
      }
    
      return $password;
    }
}
