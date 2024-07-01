<?php

namespace Drupal\custom_cache_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block displaying unused volumes.
 *
 * @Block(
 *   id = "custom_cache_api_tags",
 *   admin_label = @Translation("Latest Article Data"),
 *   category = @Translation("Custom")
 * )
 */
class LatestArticleDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new LatestArticleDataBlock.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The Plugin Id.
   * @param mixed $plugin_definition
   *   The Plugin definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Connection $database,
    AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->currentUser = $current_user;
  }

  /**
   * Set container services.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   * @param array $configuration
   *   The configuration.
   * @param string $plugin_id
   *   The Plugin Id.
   * @param mixed $plugin_definition
   *   The Plugin definition.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Fetch latest 3 articles.
    $query = $this->database->select('node_field_data', 'n')
      ->fields('n', ['nid', 'title'])
      ->condition('n.status', 1, '=')
      ->condition('n.type', 'article', '=')
      ->orderBy('n.created', 'DESC')
      ->range(0, 3);

    // Get latest 3 articles.
    $article_data = $query->execute()->fetchAll();

    $article_content = $build = [];

    if (!empty($article_data)) {
      foreach ($article_data ?: [] as $result) {
        $article_content[$result->nid] = $result->title;
        $article_cache_tags[] = 'node:' . $result->nid;
      }
    }

    $current_user_email = $this->currentUser->getEmail();

    $build = [
      '#theme' => 'latest_article_data',
      '#article_data' => $article_content,
      '#cache' => [
        // Invalidate when node 1 or term 2 updates
        'tags' => $article_cache_tags,
        'contexts' => ['user'],
      ],
      '#current_user_email' => $current_user_email,
      '#attached' => [
        'library' => [
          'custom_cache_api/cache_api_block',
        ],
      ],
    ];

    // Print cache metadata for debugging.
    // $cache_metadata = new CacheableMetadata();
    // $cache_metadata->setCacheContexts(['user']);
    // $cache_metadata->setCacheTags(['node_list']);
    // // $cache_metadata->setCacheMaxAge(CacheableMetadata::CACHE_PERMANENT);
    // dump($cache_metadata);
    // exit;

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['user']);
  }

}
