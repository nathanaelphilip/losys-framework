<?php

namespace Losys\Cache\Adapter\Transient;

use Losys\Cache\Adapter\CacheInterface;

/**
 * TransientService
 */
class Service implements CacheInterface
{

  public function get($key, $default = null)
  {
    return get_transient($key);
  }

  public function set($key, $value, $ttl = HOUR_IN_SECONDS * 1)
  {
    return set_transient($key, $value, $ttl);
  }

  public function delete($key)
  {
    return delete_transient($key);
  }

  public function clear()
  {
    return false;
  }

  public function getMultiple($keys, $default = null)
  {
    $items = [];

    foreach ($keys as $key) {
      $items[] = $this->get($key);
    }

    return $items;
  }

  public function setMultiple($values, $ttl = HOUR_IN_SECONDS * 1)
  {
    foreach ($values as $key => $value) {
      $this->set($key, $value, $ttl);
    }
  }

  public function deleteMultiple($keys)
  {
    foreach ($keys as $key) {
      $this->delete($key);
    }
  }

  public function has($key)
  {
    return (bool) $this->get($key);
  }
}
