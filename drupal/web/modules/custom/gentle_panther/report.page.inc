<?php

/**
 * @file
 * Contains report.page.inc.
 *
 * Page callback for Report entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Report templates.
 *
 * Default template: report.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_report(array &$variables) {
  // Fetch Report Entity Object.
  $report = $variables['elements']['#report'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
