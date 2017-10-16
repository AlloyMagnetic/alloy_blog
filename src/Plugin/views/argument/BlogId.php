<?php

namespace Drupal\alloy_blog\Plugin\views\argument;

use Drupal\alloy_blog\Entity\Blog;
use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Allow blog ID(s) as argument.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("blog_id")
 */
class BlogId extends NumericArgument {
  public function title() {
    if ($this->argument) {
      $blog = Blog::load($this->argument);
      if ($blog) {
        return $blog->getTitle();
      }
    }
    return $this->argument;
  }

}
