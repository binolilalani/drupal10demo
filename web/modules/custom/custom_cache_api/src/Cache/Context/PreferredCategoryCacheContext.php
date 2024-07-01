<?php

namespace Drupal\custom_cache_api\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PreferredCategoryCacheContext implements CacheContextInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(AccountProxyInterface $account, EntityTypeManagerInterface $entity_type_manager) {
    $this->account = $account;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Store preferred category ids in cache context.
   */
  public function getContext() {
    $uid = $this->account->id();
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    $preferred_categories = $user->get('field_preferred_category')->referencedEntities();
    $category_ids = array_map(function ($term) {
      return $term->id();
    }, $preferred_categories);

    return implode(',', $category_ids);
  }

  /**
   * Store cache context label.
   */
  public static function getLabel() {
    return t('Preferred Category Cache Context');
  }

  /**
   * Provides cacheability metadata.
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }
}