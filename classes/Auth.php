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
      if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        die('not logged in');
      }
      HTTP::redirect('login?return='  . urlencode(Env::get('request_url')));
    }
    
    ResponseCache::disable();
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
      if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        die('not logged in');
      }
      HTTP::redirect('login?return='  . urlencode(Env::get('request_url')));
    }
    
    ResponseCache::disable();
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
    global $db;
    self::checkConfig();
    
    if (self::$loggedInUser) {
      return self::$loggedInUser;
    }
    
    if (!isset($_COOKIE['u'])) {
      return null;
    }
    
    $sessionId = preg_replace('/[^a-z0-9]/', '', $_COOKIE['u']);
    try {
      $loginSession = $db->login_session->byId($sessionId);
    } catch (\Exception $e) {
      self::logout();
      return null;
    }
    
    // inactive for too long? kill it
    if (($loginSession->last_active->getTimestamp() + self::$inactiveLoginTimeout) < time()) {
      self::logout();
      return null;
    }
    
    // find the user
    $user = $db->user->byId($loginSession->user);
    
    // verify login session (block privilege escalation by a database injection)
    if ($loginSession->hash != sha1($loginSession->user . $user->password . $loginSession->last_active->getTimestamp() . Env::get('auth_salt'))){
      self::logout();
      return null;
    }
    
    // verify user hash
    if ($user->signature != sha1($user->id . $user->type . $user->email . $user->password . Env::get('auth_salt'))) {
      self::logout();
      return null;
    }
    
    // record activity on the login session
    $loginSession->last_active = new \DateTimeImmutable();
    $loginSession->hash = sha1($loginSession->user . $user->password . $loginSession->last_active->getTimestamp() . Env::get('auth_salt'));
    $db->login_session->write($sessionId, $loginSession, true);
    
    // bump the expiry date on the cookie
    setcookie('u', $sessionId, time() + self::$inactiveLoginTimeout, '/');
    
    // and finally, log them in
    self::$loggedInUser = $user;
    return $user;
  }
  
  static public function logout()
  {
    global $db;
    
    if (isset($_COOKIE['u'])) {
      $sessionId = preg_replace('/[^a-z0-9]/', '', $_COOKIE['u']);
      $db->login_session->purge($sessionId);
    }
    
    setcookie('u', '', time() - 3600, '/');
    unset($_COOKIE['u']);
    
    self::$loggedInUser = null;
  }
  
  static public function attemptLogin($email, $password)
  {
    global $db;
    
    self::checkConfig();
    
    // find user
    $id = sha1(strtolower($email) . Env::get('auth_salt'));
    try {
      $user = $db->user->byId($id);
    } catch (\Exception $e) {
      return false;
    }
    
    // brute force prevention
    // load all recorded login failures
    try {
      $userLoginFailuresRow = $db->login_failure->byId($id);
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
      $db->login_failure->write($id, $userLoginFailuresRow, true);
      return false;
    }
    
    // verify password
    $hashForCookie = sha1(password_hash($password, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));
    $hashForDatabase = sha1(password_hash($hashForCookie, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));
    if ($user->password != $hashForDatabase) {
      $userLoginFailuresRow->failures[] = time();
      $userLoginFailuresRow->failures = json_encode($userLoginFailuresRow->failures);
      $db->login_failure->write($id, $userLoginFailuresRow, true);
      return false;
    }
    
    // verify user hash
    $expectedSignature = sha1($user->id . $user->type . $user->email . $user->password . Env::get('auth_salt'));
    if ($user->signature != $expectedSignature) {
      $userLoginFailuresRow->failures[] = time();
      $userLoginFailuresRow->failures = json_encode($userLoginFailuresRow->failures);
      $db->login_failure->write($id, $userLoginFailuresRow, true);
      return false;
    }
    
    // log the user in
    $session = (object)[
      'user' => $id,
      'last_active' => new \DateTimeImmutable()
    ];
    $session->hash = sha1($session->user . $hashForDatabase . $session->last_active->getTimestamp() . Env::get('auth_salt'));
    $db->login_session->write($hashForCookie, $session, true);
    setcookie('u', $hashForCookie, time() + self::$inactiveLoginTimeout, '/');
    
    return true;
  }
}
