<?php

namespace Drupal\custom_cache_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Preferred Category Articles' Block.
 *
 * @Block(
 *   id = "preferred_category_articles_block",
 *   admin_label = @Translation("Preferred Category Articles Block"),
 * )
 */
class PreferredCategoryArticlesBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

  /** Construct PreferredCategoryArticlesBlock object.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $account, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->account = $account;
    $this->entityTypeManager = $entity_type_manager;

  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager')

    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $uid = $this->account->id();
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    $preferred_categories = $user->get('field_preferred_category')->referencedEntities();
    if (!empty($preferred_categories)) {
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
      $query->accessCheck(TRUE);
      $query->condition('type', 'article');
      $query->condition('status', 1);
      $query->condition('field_category.entity.tid', array_map(function ($term) {
        return $term->id();
      }, $preferred_categories));

      $nids = $query->execute();

      if (empty($nids)) {
        return;
      }

      // Load multiple nodes.
      $article_nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

      $articles = [];
      foreach ($article_nodes as $article_node) {
        $articles[] = [
          '#node' => $article_node,
        ];
      }

      return [
        '#theme' => 'preferred_category_articles',
        '#items' => $articles,
        '#cache' => [
          'contexts' => ['preferred_category'],
        ],
      ];
    }
    else {
      return [];
    }
  }
}