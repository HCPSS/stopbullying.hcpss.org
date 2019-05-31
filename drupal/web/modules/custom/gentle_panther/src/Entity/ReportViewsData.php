<?php

namespace Drupal\gentle_panther\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Report entities.
 */
class ReportViewsData extends EntityViewsData {

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
