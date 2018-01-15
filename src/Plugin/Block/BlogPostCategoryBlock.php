<?php

namespace Drupal\alloy_blog\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "blog_post_category",
 *   admin_label = @Translation("Blog Post Category"),
 *   category = @Translation("Blog"),
 * )
 */
class BlogPostCategoryBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if (!$node || $node->getType() != 'blog_post') {
      return;
    }

    $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $output = $viewBuilder->viewField($node->field_categories, 'full');
    return [
      'content' => $output,
      '#title' => '',
    ];
  }

  public function getCacheTags() {
    // With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      // if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      // Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    // if you depends on \Drupal::routeMatch()
    // you must set context of this block with 'route' context tag.
    // Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

}
