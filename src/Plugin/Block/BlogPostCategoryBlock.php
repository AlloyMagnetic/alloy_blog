<?php

namespace Drupal\alloy_blog\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    $output['#cache']['tags'] = $node->getCacheTags();
    return [
      'content' => $output,
      '#title' => '',
    ];
  }

}
