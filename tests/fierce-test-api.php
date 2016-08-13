<?php

namespace Fierce;

$db = $this->mock('DB');
Env::push('db', $db);

$db->User = $this->Mock('DBEntity', 'User');
$db->LoginFailure = $this->Mock('DBEntity', 'LoginFailure');
$db->ApiToken = $this->Mock('DBEntity', 'ApiToken');

$api = new ApiController();
$api->rethrowExceptions = false;
$api->db = $db;

// create token with correct credentials
$email = 'justine@example.com';
$password = 'osh2W4gMwxhEaD';

$user = (object)[
  'id' => Auth::userIdByEmail($email),
  'type' => 'test',
  'email' => $email,
  'password' => Auth::hashForPassword($email, $password)
];
$user->signature = Auth::signatureForUser($user);


$db->prepareForCall('id', null, function() {
  return sha1(rand());
});
$db->User->prepareForCall('byId', null, function() use ($user) {
  return $user;
});
$db->LoginFailure->prepareForCall('byId', null, function () {
  throw new \Exception("Invalid id");
});
$db->ApiToken->prepareForCall('write');

ob_start();

$api->apiParams = (object)[
  'email' => $email,
  'password' => $password
];
$api->runActionNamed('createApiToken');

$responseJson = ob_get_clean();
$response = json_decode($responseJson);

$this->assert($response != null, 'Valid API response');

$this->assertEqual($response->error, false, 'Create API Token without error');
$this->assert(is_string($response->authToken), 'Test create API Token string');
$this->assertEqual($response->userId, $user->id, 'Test create API Token user id');


// create token with invalid email
$db->LoginFailure->prepareForCall('write');

$email = 'foo';
$response = $api->createApiTokenAction((object)[
  'email' => $email,
  'password' => $password
]);
$this->assertEqual($response, [
  'error' => 'Invalid Credentials'
], 'Test create API Token with incorrect email');


// create token with invalid password
$email = 'justine@example.com';
$password = 'bar';
$response = $api->createApiTokenAction((object)[
  'email' => $email,
  'password' => $password
]);
$this->assertEqual($response, [
  'error' => 'Invalid Credentials'
], 'Test create API Token with incorrect password');
