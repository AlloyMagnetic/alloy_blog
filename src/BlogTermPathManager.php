<?php

namespace Drupal\alloy_blog;

use Drupal\taxonomy\Entity\Term;
use Drupal\alloy_blog\Entity\Blog;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

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
    $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;
    $blog_slug = $blog->get('field_url_slug')->value;

    $full_alias = '/' . $blog_slug . $this->getTermAlias($term);
    $alias_target = '/alloy/blog-term/' . $blog->id() . '/' . $term->id();

    // If there is an existing alias for this path, delete it first. Failing to
    // do so will either create duplicates or conflicts.
    $delete_conditions = [
      'alias' => $full_alias
    ];
    $existing_aliases = \Drupal::entityTypeManager()->getStorage('path_alias')->loadByProperties($delete_conditions);
    \Drupal::entityTypeManager()->getStorage('path_alias')->delete($existing_aliases);

    // If there is an existing path for this alias, delete it first. Failing to
    // do so will either create duplicates or conflicts.
    $delete_conditions = [
      'path' => $alias_target,
    ];
    $existing_aliases = \Drupal::entityTypeManager()->getStorage('path_alias')->loadByProperties($delete_conditions);
    \Drupal::entityTypeManager()->getStorage('path_alias')->delete($existing_aliases);

    try {
      $path_alias_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
      $path_alias = $path_alias_storage->create([
        'path' => $alias_target,
        'langcode' => $langcode,
      ]);
      $path_alias->setAlias($full_alias);
      $path_alias->save();
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
    foreach ($this->entity_load_multiple('blog') as $blog) {
      $this->updateAlias($term, $blog);
    }
  }

  // Create aliases for a single blog. An idempotent function that creates
  // aliases for all terms
  public function updateBlogAliases(Blog $blog) {
    foreach($this->entity_load_multiple('taxonomy_term') as $term) {
      $this->updateAlias($term, $blog);
    }
  }

  // Create aliases for all terms
  public function updateAllAliases() {
    foreach($this->entity_load_multiple('taxonomy_term') as $term) {
      foreach ($this->entity_load_multiple('blog') as $blog) {
        $this->updateAlias($term, $blog);
      }
    }
  }

  // Gets the current alias for the given term
  private function getTermAlias(Term $term) {
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/taxonomy/term/' . $term->id());
    return $alias;
  }

  function entity_load_multiple($entity_type, array $ids = NULL, $reset = FALSE) {
    $controller = \Drupal::entityTypeManager()
      ->getStorage($entity_type);
    if ($reset) {
      $controller
        ->resetCache($ids);
    }
    return $controller
      ->loadMultiple($ids);
  }

}
