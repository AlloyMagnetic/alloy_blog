<?php

/**
 * @file
 * Contains blog.page.inc.
 *
 * Page callback for Blog entities.
 */

use Drupal\Core\Render\Element;
use Drupal\views\Views;

/**
 * Prepares variables for Blog templates.
 *
 * Default template: blog.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_blog(array &$variables) {
  // Fetch Blog Entity Object.
  $blog = $variables['elements']['#blog'];
  $id = $blog->get('id')->value;

  $args = [$id];
  $view = Views::getView('blog');
  $display = 'blog_listing';
  if (is_object($view)) {
    $view->setArguments($args);
    $view->setDisplay($display);
    $view->preExecute();
    $view->execute();
    $variables['listing'] = $view->buildRenderable($display, $args);
  }

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
