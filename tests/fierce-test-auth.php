<?php

namespace Fierce;

$cookieManager = $this->mock('CookieManager');
Env::push('cookie', $cookieManager);

$db = $this->mock('DB');
Env::push('db', $db);

$db->User = $this->mock('DBEntity', 'user');
$db->LoginFailure = $this->mock('DBEntity', 'login failure');
$db->LoginSession = $this->mock('DBEntity');
$db->ApiToken = $this->mock('DBEntity', 'ApiToken');


// search for user by email
$db->User->prepareForCall('find', ['email' => 'foo'], (object)['id' => 'bar']);
$this->assertEqual(Auth::userIdByEmail('foo'), 'bar', 'Verify search for user by email');


// password hashing
$email = 'justine';
$password = 'sane';
$type = 'admin';
$id = $id = Auth::userIdByEmail($email);
$passwordHash = sha1(password_hash($password, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id]));
$passwordHash = sha1(password_hash($passwordHash, PASSWORD_BCRYPT, ['cost' => 11, 'salt' => $id])); // for historical reasons we have to double hash... would be good to remove this in future, but complicated without resetting passwords

$start = microtime(true);
$gotPasswordHash = Auth::hashForPassword('justine', 'sane');
$duration = abs($start - microtime(true));

$this->assertEqual($gotPasswordHash, $passwordHash, 'Verify password hashing');

$this->assert($duration > 0.3, 'Verify decent hash strength');

// user signatures
$expectedSignature = sha1($id . $type . $email . $passwordHash . Env::get('auth_salt'));
$signature = Auth::signatureForUser((object)[
  'id' => $id,
  'type' => $type,
  'email' => $email,
  'password' => $passwordHash
]);
$this->assertEqual($expectedSignature, $signature, 'User signature generation');

// brute force
$this->fail("Brute force detection"); // todo: test this

// create session/api token
$user = (object)[
  'id' => $id,
  'password' => $passwordHash
];
$tokenId = '419db9a8d05cf8657adc2e92b1fc98cd9c0054cf';
$date = new \DateTime();
$tokenHash = Auth::tokenHashForUser($user, $date, $tokenId);

$this->assertEqual($tokenHash, sha1($user->id . $user->password . $date->getTimestamp() . Env::get('auth_salt')), 'Verify creating session/api tokens');




// test using an API token
$user = (object)[
  'id' => $id,
  'type' => $type,
  'name' => 'Justine',
  'email' => $email,
  'password' => $passwordHash,
  'signature' => $signature
];
$apiToken = (object)[
  'id' => $tokenId,
  'userId' => $user->id,
  'created' => $date,
  'hash' => $tokenHash
];

$db->User->prepareForCall('byId', [$user->id], $user);
$db->ApiToken->prepareForCall('byId', [$apiToken->id], $apiToken);

$gotUser = Auth::userForApiToken($apiToken->id);
$this->assertEqual($gotUser, $user, "Access user by API token");



// incorrect username
$email = 'justin';
$id = $id = Auth::userIdByEmail($email);

$db->User->prepareForCall('byId', [$id], function() {
  throw new \Exception("Invalid id");
});

$db->LoginFailure->prepareForCall('byId', null, function() {
  throw new \Exception("Invalid id");
});
$db->LoginFailure->prepareForCall('write');

$returnedId = Auth::userIdIfCredentialsValid($email, $password);
$this->assertEqual($returnedId, null, 'Verify incorrect email fails');


// correct username, incorrect password
$email = 'justine';
$password = 'san';
$id = Auth::userIdByEmail($email);
$db->User->prepareForCall('byId', [$id], (object)[
  'id' => $id,
  'type' => 'test',
  'email' => 'justine',
  'password' => $passwordHash,
  'signature' => '8e82e581a143b6f8d30206891ca2549214e05afc'
]);

$returnedId = Auth::userIdIfCredentialsValid($email, $password);
$this->assertEqual($returnedId, null, 'Verify incorrect password fails');

// correct password
$password = 'sane';
$returnedId = Auth::userIdIfCredentialsValid($email, $password);
$this->assertEqual($returnedId, $id, 'Verify correct credentials succeed');


// no cookie should mean no logged in user
$cookieManager->prepareForCall('get', ['u'], null);
$u = Auth::loggedInUser();

$this->assertEqual($u, null, "Check for no logged in user");


// logged in user with bad cookie should mean no logged in user
$cookieManager->prepareForCall('get', ['u'], 'blah');
$cookieManager->prepareForCall('clear', ['u']);
$db->LoginSession->prepareForCall('byId', ['blah'], function() {
  throw new \Exception("Invalid id");
});
$db->LoginSession->prepareForCall('purge', ['blah']);

$u = Auth::loggedInUser();

$this->assertEqual($u, null, "Check for no logged in user with invalid cookie");

Env::pop('cookie');


// more!
$this->fail('TODO: test more stuff');


Env::pop('db');
