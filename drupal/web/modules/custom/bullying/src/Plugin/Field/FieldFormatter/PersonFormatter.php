<?php

namespace Drupal\bullying\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Person formatter.
 *
 * @FieldFormatter(
 *   id = "person_formatter",
 *   label = @Translation("Person formatter"),
 *   field_types = {
 *     "person"
 *   }
 * )
 */
class PersonFormatter extends FormatterBase {

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\FormatterBase::settingsSummary()
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('A person.');

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
      $markup .= $item->age ? $item->age . '<br>' : '';
      $markup .= $item->school ? $item->school . '<br>' : '';
      $markup .= $item->student ? $item->student . '<br>' : '';

      $element[$delta] = ['#markup' => $markup];
    }

    return $element;
  }
}
