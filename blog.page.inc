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
  $variables['content']['listing'] = NULL;
  // Fetch Blog Entity Object.
  if (!isset($variables['elements']['#blog'])) {
    return;
  }

  if (isset($variables['elements']['#blog_ids'])) {
    $ids = $variables['elements']['#blog_ids'];
  }
  else {
    $ids = [$variables['elements']['#blog']->id()];
  }

  $display = isset($variables['elements']['#display']) ? $variables['elements']['#display'] : 'blog_listing';

  // Add listing
  $args = [implode('+', $ids)];
  $view = Views::getView('blog');
  $display = $display;
  if (is_object($view)) {
    $view->setArguments($args);
    $view->setDisplay($display);
    $view->preExecute();
    $view->execute();
    $variables['content']['listing'] = $view->buildRenderable($display, $args);
  }

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
