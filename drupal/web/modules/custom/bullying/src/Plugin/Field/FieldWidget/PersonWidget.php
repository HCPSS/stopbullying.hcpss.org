<?php

namespace Drupal\bullying\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * A person widget
 *
 * @FieldWidget(
 *   id = "person_widget",
 *   label = @Translation("Person widget"),
 *   field_types = {
 *     "person"
 *   }
 * )
 */
class PersonWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\WidgetInterface::formElement()
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];

    $element += ['#type' => 'fieldgroup'];

    $element['name'] = [
      '#type' => 'textfield',
      '#default_value' => isset($item->name) ? $item->name : '',
      '#title' => $this->t('Name'),
      '#required' => FALSE,
    ];

    $element['school'] = [
      '#type' => 'textfield',
      '#default_value' => isset($item->school) ? $item->school : '',
      '#title' => $this->t('School'),
      '#autocomplete_route_name' => 'bullying.school.autocomplete',
      '#required' => FALSE,
    ];

    $element['age'] = [
      '#type' => 'number',
      '#default_value' => isset($item->age) ? $item->age : NULL,
      '#title' => $this->t('Age'),
      '#required' => FALSE,
     ];

    if ($this->getSetting('showStudent')) {
      $element['student'] = [
        '#type' => 'checkbox',
        '#default_value' => isset($item->student) ? $item->student : '',
        '#title' => $this->t('Student'),
        '#required' => FALSE,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'showStudent' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\WidgetBase::settingsForm()
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['showStudent'] = [
      '#title' => $this->t('Show student field'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showStudent'),
    ];

    return $elements;
  }
}
