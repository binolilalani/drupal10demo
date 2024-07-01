<?php

$memcache = new Memcached();
$memcache->addServer('127.0.0.1', 11211);

// Set a test value
$memcache->set('test_key', 'test_value');

// Get the test value
$value = $memcache->get('test_key');

if ($value === 'test_value') {
  echo "Memcache is working!";
} else {
  echo "Memcache is not working.";
}
?>