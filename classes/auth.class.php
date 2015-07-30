<?

/**
 * 
 * Fierce Web Framework
 * https://github.com/abhibeckert/Fierce
 *
 * This is free and unencumbered software released into the public domain.
 * For more information, please refer to http://unlicense.org
 * 
 */

class Auth
{
  private static $loggedInUser = null;
  
  public static function requireAdmin()
  {
    if (!self::haveAdmin()) {
      if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        die('not logged in');
      }
      HTTP::redirect('login?return='  . urlencode(REQUEST_URL));
    }
    
    ResponseCache::disable();
  }
  
  public static function haveAdmin()
  {
    if (!$user = self::loggedInUser()) {
      return false;
    }
    
    if ($user->type != 'admin' && $user->type != 'root') {
      return false;
    }
    
    return true;
  }
  
  public static function requireRoot()
  {
    if (!self::haveRoot()) {
      if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        die('not logged in');
      }
      HTTP::redirect('login?return='  . urlencode(REQUEST_URL));
    }
    
    ResponseCache::disable();
  }
  
  public static function haveRoot()
  {
    if (!$user = self::loggedInUser()) {
      return false;
    }
    
    if ($user->type != 'root') {
      return false;
    }
    
    return true;
  }
  
  public static function loggedInUser()
  {
    global $db;
    
    if (self::$loggedInUser) {
      return self::$loggedInUser;
    }
    
    if (!isset($_COOKIE['u'])) {
      return null;
    }
    
    $sessionId = preg_replace('/[^a-z0-9]/', '', $_COOKIE['u']);
    try {
      $loginSession = $db->login_session->byId($sessionId);
    } catch (Exception $e) {
      self::logout();
      return null;
    }
    
    // inactive for 2 hours? kill it
    if (($loginSession->last_active + 7200) < time()) {
      self::logout();
      return null;
    }
    
    // find the user
    $user = $db->user->byId($loginSession->user);
    
    // verify login session (block privilege escalation by a database injection)
    if ($loginSession->hash != sha1($loginSession->user . $user->password . $loginSession->last_active . AUTH_SALT)){
      self::logout();
      return null;
    }
    
    // verify user hash
    if ($user->signature != sha1($user->id . $user->type . $user->email . $user->password . AUTH_SALT)) {
      self::logout();
      return null;
    }
    
    // record activity on the login session
    $loginSession->last_active = time();
    $loginSession->hash = sha1($loginSession->user . $user->password . $loginSession->last_active . AUTH_SALT);
    $db->login_session->write($sessionId, $loginSession, true);
    
    // and finally, log them in
    self::$loggedInUser = $user;
    return $user;
  }
  
  public static function logout()
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
  
  public static function attemptLogin($email, $password)
  {
    global $db;
    
    // find user
    $id = sha1(strtolower($email) . AUTH_SALT);
    try {
      $user = $db->user->byId($id);
    } catch (Exception $e) {
      return false;
    }
    
    // brute force prevention
    // load all recorded login failures
    try {
      $userLoginFailuresRow = $db->login_failures->byId($id);
    } catch (Exception $e) {
      $userLoginFailuresRow = (object)['failures' => []];
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
      $db->login_failures->write($id, $userLoginFailuresRow, true);
      return false;
    }
    
    // verify password
    $hashForCookie = sha1(password_hash($password, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));
    $hashForDatabase = sha1(password_hash($hashForCookie, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));	
  
    if ($user->password != $hashForDatabase) {
      $userLoginFailuresRow->failures[] = time();
      $db->login_failures->write($id, $userLoginFailuresRow, true);
      return false;
    }
    
    // verify user hash
    if ($user->signature != sha1($user->id . $user->type . $user->email . $user->password . AUTH_SALT)) {
      $userLoginFailuresRow->failures[] = time();
      $db->login_failures->write($id, $userLoginFailuresRow, true);
      return false;
    }
    
    // log the user in
    $session = (object)[
      'user' => $id,
      'last_active' => time()
    ];
    $session->hash = sha1($session->user . $hashForDatabase . $session->last_active . AUTH_SALT);
    $db->login_session->write($hashForCookie, $session, true);
    setcookie('u', $hashForCookie, 0, '/');
    
    return true;
  }
}
