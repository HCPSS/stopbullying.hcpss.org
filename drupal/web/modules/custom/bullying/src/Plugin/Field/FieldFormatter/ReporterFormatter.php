<?php

namespace Drupal\bullying\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Reporter formatter.
 *
 * @FieldFormatter(
 *   id = "reporter_formatter",
 *   label = @Translation("Reporter formatter"),
 *   field_types = {
 *     "reporter"
 *   }
 * )
 */
class ReporterFormatter extends FormatterBase {

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\FormatterBase::settingsSummary()
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('A person making a report.');

    return $summary;
  }

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\FormatterInterface::viewElements()
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $markup = '';

      $markup .= $item->name ? $item->name . '<br>' : '';
      $markup .= $item->email ? $item->email . '<br>' : '';
      $markup .= $item->school ? $item->school . '<br>' : '';
      $markup .= $item->telephone ? $item->telephone . '<br>' : '';
      $markup .= $item->role ? $item->role . '<br>' : '';

      $element[$delta] = ['#markup' => $markup];
    }

    return $element;
  }
}
