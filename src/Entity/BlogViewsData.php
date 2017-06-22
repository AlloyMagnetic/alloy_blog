<?php

namespace Drupal\alloy_blog\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Blog entities.
 */
class BlogViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
