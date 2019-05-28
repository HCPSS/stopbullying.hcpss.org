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
 *   id = "reporter_widget",
 *   label = @Translation("Reporter widget"),
 *   field_types = {
 *     "reporter"
 *   }
 * )
 */
class ReporterWidget extends WidgetBase implements WidgetInterface {

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\WidgetInterface::formElement()
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element += ['#type' => 'fieldgroup'];

    $element['name'] = [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->name) ? $items[$delta]->name : '',
      '#title' => $this->t('Name'),
      '#required' => FALSE,
    ];

    $element['telephone'] = [
      '#type' => 'tel',
      '#default_value' => isset($items[$delta]->telephone) ? $items[$delta]->telephone : NULL,
      '#title' => $this->t('Telephone'),
      '#required' => FALSE,
    ];

    $element['email'] = [
      '#type' => 'email',
      '#default_value' => isset($items[$delta]->email) ? $items[$delta]->email : '',
      '#title' => $this->t('Email'),
      '#required' => FALSE,
    ];

    $element['school'] = [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->school) ? $items[$delta]->school : '',
      '#title' => $this->t('School'),
      '#autocomplete_route_name' => 'bullying.school.autocomplete',
      '#required' => FALSE,
    ];

    $element['role'] = [
      '#type' => 'radios',
      '#default_value' => isset($items[$delta]->role) ? $items[$delta]->role : '',
      '#title' => $this->t('Role'),
      '#options' => [
        'Student' => $this->t('Student'),
        'Parent/guardian of a student' => $this->t('Parent/guardian of a student'),
        'Close adult relative of a student' => $this->t('Close adult relative of a student'),
        'School Staff' => $this->t('School Staff'),
        'Bystander' => $this->t('Bystander'),
      ],
      '#required' => FALSE,
    ];

    return $element;
  }
}
