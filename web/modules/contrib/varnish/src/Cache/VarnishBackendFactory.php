<?php

namespace Drupal\varnish\Cache;

use Drupal\Core\Cache\CacheFactoryInterface;

class VarnishBackendFactory implements CacheFactoryInterface{

  /**
   * Instantiated varnish cache bins.
   *
   * @var \Drupal\varnish\Cache\VarnishBackend[]
   */
  protected $bins = [];

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    if (!isset($this->bins[$bin])) {
      $this->bins[$bin] = new VarnishBackend($bin);
    }
    return $this->bins[$bin];
  }

}
