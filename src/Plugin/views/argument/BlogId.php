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
      return Blog::load($this->argument)->label();
    }
    return $this->argument;
  }

}
