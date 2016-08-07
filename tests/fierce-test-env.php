<?php

namespace Fierce;

try {
  Env::get('_test_foo');
  
  $this->assert(false, 'Exception accessing unknown Env value');
} catch (\Exception $e) {
  $this->assert(true, 'Exception accessing unknown Env value');
}

Env::set('_test_foo', 'bar');
$this->assertEqual(Env::get('_test_foo'), 'bar', 'Read Env value');

Env::set('_test_foo', 'a', 10);
$this->assertEqual(Env::get('_test_foo'), 'a', 'Apply higher priority value');


Env::set('_test_foo', 'b', -5);
$this->assertEqual(Env::get('_test_foo'), 'a', 'Ignore low priority value');

Env::push('_test_foo', 'c');
$this->assertEqual(Env::get('_test_foo'), 'c', 'Push env value');

Env::push('_test_foo', 'd');
$this->assertEqual(Env::get('_test_foo'), 'd', 'Push another env value');

Env::pop('_test_foo');
$this->assertEqual(Env::get('_test_foo'), 'c', 'Pop env value');

Env::pop('_test_foo');
$this->assertEqual(Env::get('_test_foo'), 'a', 'Pop another env value');

try {
  Env::pop('_test_foo');
  
  $this->assert(false, 'Exception popping too many times');
} catch (\Exception $e) {
  $this->assert(true, 'Exception popping too many times');
}

try {
  Env::pop('_test_bar', 'c');
  
  $this->assert(false, 'Exception popping unknown Env value');
} catch (\Exception $e) {
  $this->assert(true, 'Exception popping unknown Env value');
}