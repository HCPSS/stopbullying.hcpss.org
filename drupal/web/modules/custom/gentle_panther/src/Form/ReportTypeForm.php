<?php

namespace Drupal\gentle_panther\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ReportTypeForm.
 */
class ReportTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $report_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $report_type->label(),
      '#description' => $this->t("Label for the Report type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $report_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\gentle_panther\Entity\ReportType::load',
      ],
      '#disabled' => !$report_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $report_type = $this->entity;
    $status = $report_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Report type.', [
          '%label' => $report_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Report type.', [
          '%label' => $report_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($report_type->toUrl('collection'));
  }

}
