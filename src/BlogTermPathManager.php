<?php

namespace Drupal\alloy_blog;

use Drupal\Core\Entity\EntityInterface;
use Drupal\alloy_blog\Entity\Blog;
use Drupal\taxonomy\Entity\Term;

class BlogTermPathManager {

  public function blogOrTermEdit(EntityInterface $entity) {
    if ($entity->getEntityTypeId() == 'taxonomy_term') {
      // We have to do this here because module weight is something we can't set
      // in default config.
      \Drupal::service('pathauto.generator')->updateEntityAlias($entity, 'update');
      $this->updateTermAliases($entity);
    }
    if ($entity->getEntityTypeId() == 'blog') {
      $this->updateBlogAliases($entity);
    }
  }

  public function updateAlias(Term $term, Blog $blog) {
    $blog_slug = $blog->get('field_url_slug')->value;
    if (!$blog_slug) {
      $blog_slug = \Drupal::service('pathauto.alias_cleaner')->cleanString($blog->title->value);
      $blog->field_url_slug = $blog_slug;
      $blog->save();
    }

    $full_alias = '/' . $blog_slug . $this->getTermAlias($term);
    $alias_target = '/alloy/blog-term/' . $blog->id() . '/' . $term->id();

    // If there is an existing alias for this path, delete it first. Failing to
    // do so will either create duplicates or conflicts.
    $delete_conditions = [
      'alias' => $full_alias
    ];
    \Drupal::service('path.alias_storage')->delete($delete_conditions);

    // If there is an existing path for this alias, delete it first. Failing to
    // do so will either create duplicates or conflicts.
    $delete_conditions = [
      'source' => $alias_target,
    ];
    \Drupal::service('path.alias_storage')->delete($delete_conditions);

    try {
      \Drupal::service('path.alias_storage')->save($alias_target, $full_alias);
      $message = 'Alias created: ' . $full_alias . ' > ' . $alias_target;
      if (function_exists('drush_print_r')) {
        drush_print_r($message);
      }
      \Drupal::logger('Alloy Blog')->notice($message);
      return true;
    }
    catch (\Exception $e) {
      $message = 'Could not create alias: ' . $full_alias . ' > ' . $alias_target . '. ' . $e->getMessage();
      if (function_exists('drush_print_r')) {
        drush_print_r($message);
      }
      \Drupal::logger('Alloy Blog')->error($message);
      return false;
    }
  }

  // Create aliases for a single term. An idempotent function that creates
  // aliases for all blogs
  public function updateTermAliases(Term $term) {
    foreach (entity_load_multiple('blog') as $blog) {
      $this->updateAlias($term, $blog);
    }
  }

  // Create aliases for a single blog. An idempotent function that creates
  // aliases for all terms
  public function updateBlogAliases(Blog $blog) {
    foreach(entity_load_multiple('taxonomy_term') as $term) {
      $this->updateAlias($term, $blog);
    }
  }

  // Create aliases for all terms
  public function updateAllAliases() {
    foreach(entity_load_multiple('taxonomy_term') as $term) {
      foreach (entity_load_multiple('blog') as $blog) {
        $this->updateAlias($term, $blog);
      }
    }
  }

  // Gets the current alias for the given term
  private function getTermAlias(Term $term) {
    $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/taxonomy/term/' . $term->id());
    return $alias;
  }

}
