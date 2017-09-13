<?php

namespace Drupal\alloy_blog\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\alloy_blog\Entity\Blog;
use Drupal\taxonomy\Entity\Term;

class BlogTermContentController extends ControllerBase {
  public function getContent(Blog $blog, Term $term) {
    $content = [];
    $content['view'] = views_embed_view('blog_term_content');
    return $content;
  }

  public function getTitle(Blog $blog, Term $term) {
    return $blog->getTitle() . ': ' . $term->getName();
  }

}
