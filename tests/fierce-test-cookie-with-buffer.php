<?php

namespace Fierce;

$c = new CookieManager();


// set a session cookie and check if it was set
$c->set('foo', 'bar');
$this->assertContains(headers_list(), 'Set-Cookie: foo=bar; path=/', 'Send session cookie');
$this->assertEqual($_COOKIE['foo'], 'bar', "Set session cookie");
$this->assertEqual($c->get('foo'), 'bar', "Get session cookie");
header_remove('Set-Cookie');


// set a non-session cookie and check if it was set with correct expiry
$c->set('foo', 'bar', 42);
$datetime = date('D, d-M-Y H:i:s', time() + 42) . ' GMT';
$this->assertContains(headers_list(), 'Set-Cookie: foo=bar; expires=' . $datetime . '; Max-Age=42; path=/', 'Send cookie');
$this->assertEqual($_COOKIE['foo'], 'bar', "Set cookie");
$this->assertEqual($c->get('foo'), 'bar', "Get cookie");
header_remove('Set-Cookie');


// set a cookie then clear it, make sure it clears
$c->set('foo', 'bar');
$c->clear('foo');
$this->assertEqual($c->get('foo'), null, "Cookie cleared");
$this->assert(!isset($_COOKIE['foo']), "Cookie cleared from \$_COOKIE");
header_remove('Set-Cookie');


// check default value
$this->assertEqual($c->get('foo', 'bob'), 'bob', "Check default cookie value");
