<?php

namespace Drupal\bullying\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\bullying\Service\SchoolQueryService;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\bullying\Service\NameGeneratorService;

/**
 * Provide a person field type.
 *
 * @FieldType(
 *   id = "person",
 *   label = @Translation("Person field"),
 *   default_formatter = "person_formatter",
 *   default_widget = "person_widget",
 * )
 */
class PersonItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'));

    $properties['school'] = DataDefinition::create('string')
      ->setLabel(t('School'));

    $properties['age'] = DataDefinition::create('integer')
      ->setLabel(t('Age'));

    $properties['student'] = DataDefinition::create('boolean')
      ->setLabel(t('Student'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'name' => [
          'type' => 'varchar',
          'length' => 255,
          'default' => NULL,
        ],
        'school' => [
          'type' => 'varchar',
          'length' => 255,
          'default' => NULL,
        ],
        'age' => [
          'type' => 'int',
          'default' => NULL,
        ],
        'student' => [
          'type' => 'int',
          'default' => NULL,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $schools = SchoolQueryService::getNames();

    $values = [
      'name' => NameGeneratorService::generate(),
      'school' => $schools[array_rand($schools, 1)],
      'age' => rand(4, 100),
    ];

    if ($field_definition->getSetting('showStudent')) {
      $values['student'] = rand(0, 1) === 1;
    }

    return $values;
  }

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\Field\FieldItemBase::fieldSettingsForm()
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['showStudent'] = [
      '#title' => $this->t('Show student field'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showStudent'),
    ];

    return $element;
  }

  /**
   * {@inheritDoc}
   */
  public static function defaultFieldSettings() {
    return [
      'showStudent' => TRUE,
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\TypedData\Plugin\DataType\Map::isEmpty()
   */
  public function isEmpty() {
    $name    = $this->get('name')->getValue();
    $school  = $this->get('school')->getValue();
    $age     = $this->get('age')->getValue();

    return ($name === NULL || $name === '')
      && ($school === NULL || $school === '')
      && ($age === NULL || $age === '');
  }
}
