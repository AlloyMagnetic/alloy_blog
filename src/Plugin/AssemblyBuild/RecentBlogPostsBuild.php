<?php

namespace Drupal\alloy_blog\Plugin\AssemblyBuild;

use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\assembly\Plugin\AssemblyBuildView;
use Drupal\views\Views;
use Drupal\taxonomy\Entity\Term;
use Drupal\alloy_blog\Entity\Blog;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;

/**
 * Adds recent blog posts view to the built entity
 *  @AssemblyBuild(
 *   id = "recent_blog_posts",
 *   types = { "recent_blog_posts" },
 *   label = @Translation("Recent Blog Posts view")
 * )
 */
class RecentBlogPostsBuild extends AssemblyBuildView {
  protected function views() {
    return ['recent_blog_posts' => ['view' => 'blog', 'display' => 'recent']];
  }

  protected function argumentMappings() {
    return [
      'field_filter_by_blog' => [
        'index' => 0,
        'multiple' => 'or',
      ],
      'field_filter_by_category' => [
        'index' => 1,
        'multiple' => 'or',
      ],
      'field_filter_by_author' => [
        'index' => 2,
        'multiple' => 'or',
      ],
    ];
  }

  public function build(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {
    parent::build($build, $entity, $display, $view_mode);

    $args = $this->prepareArguments('recent_blog_posts', $this->argumentMappings(), $entity, FALSE);

    // If the filter is a single blog, single term, or single blog+term, use the
    // seo friendly url aliases for the More Blog Posts link
    $route_params = [];
    $blog_count = $args[0] == 'all' ? 0 : count($args[0]);
    $term_count = $args[1] == 'all' ? 0 : count($args[1]);

    $blog = $blog_count == 1 ? Blog::load(reset($args[0])) : FALSE;
    $term = $term_count == 1 ? Term::load(reset($args[1])) : FALSE;

    $text = "More blog posts";
    $route = 'view.blog.global';

    // filter by blog, no terms
    if ($blog && !$term) {
      $route = 'entity.blog.canonical';
      $route_params = ['blog' => $blog->id()];
      $text = t('More @title blog posts', ['@title' => $blog->getTitle()]);
    }
    // filter by terms no blog
    else if (!$blog && $term) {
      $route = 'entity.taxonomy_term.canonical';
      $route_params = ['taxonomy_term' => $term->id()];
      $text = t('More @title blog posts', ['@title' => $term->getName()]);
    }
    // filter by both
    if ($blog && $term) {
      $link = Link::fromTextAndUrl($text, $view->getUrl($this->prepareArguments('recent_blog_posts', $this->argumentMappings(), $entity), 'blog_term'));
    }
    else {
      $link = Link::fromTextAndUrl($text, Url::fromRoute($route, $route_params));
    }

    $link->getUrl()->setOption('attributes', ['class' => ['blog--more-link']]);
    $build['more']['#weight'] = 100;
    $build['more']['link'] = $link->toRenderable();
  }
}
