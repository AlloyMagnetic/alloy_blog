<?php

function alloy_blog_drush_command() {
  $items = [];
  $items['alloy-blog-generate-term-aliases'] = [
    'description' => 'Generate aliases for all blog/term pairs',
    'aliases' => ['ab:gta'],
  ];
  return $items;
}

function drush_alloy_blog_generate_term_aliases() {
  \Drupal::service('alloy_blog.blog_term_path_manager')->updateAllAliases();
}
