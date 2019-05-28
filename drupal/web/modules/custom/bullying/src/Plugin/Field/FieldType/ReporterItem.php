<?php

namespace Drupal\bullying\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Component\Utility\Random;
use Drupal\bullying\Service\SchoolQueryService;
use Drupal\bullying\Service\NameGeneratorService;

/**
 * Provide a person field type.
 *
 * @FieldType(
 *   id = "reporter",
 *   label = @Translation("Reporter field"),
 *   default_formatter = "reporter_formatter",
 *   default_widget = "reporter_widget",
 * )
 */
class ReporterItem extends FieldItemBase implements FieldItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'));

    $properties['email'] = DataDefinition::create('string')
      ->setLabel(t('Email'));

    $properties['telephone'] = DataDefinition::create('string')
      ->setLabel(t('Telephone'));

    $properties['school'] = DataDefinition::create('string')
      ->setLabel(t('School'));

    $properties['role'] = DataDefinition::create('string')
      ->setLabel(t('Role'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $schools = SchoolQueryService::getNames();

    $roles = [
      'Student',
      'Parent/guardian of a student',
      'Close adult relative of a student',
      'School Staff',
      'Bystander',
    ];

    $values = [
      'name' => NameGeneratorService::generate(),
      'email' => $random->name() . '@example.com',
      'school' => $schools[array_rand($schools, 1)],
      'telephone' => rand(pow(10, 8), pow(10, 9) - 1),
      'role' => $roles[array_rand($roles, 1)],
    ];

    return $values;
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
        'email' => [
          'type' => 'varchar',
          'length' => 255,
          'default' => NULL,
        ],
        'telephone' => [
          'type' => 'varchar',
          'length' => 255,
          'default' => NULL,
        ],
        'school' => [
          'type' => 'varchar',
          'length' => 255,
          'default' => NULL,
        ],
        'role' => [
          'type' => 'varchar',
          'length' => 255,
          'default' => NULL,
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   * @see \Drupal\Core\TypedData\Plugin\DataType\Map::isEmpty()
   */
  public function isEmpty() {
    $name      = $this->get('name')->getValue();
    $school    = $this->get('school')->getValue();
    $email     = $this->get('email')->getValue();
    $telephone = $this->get('telephone')->getValue();
    $role      = $this->get('role')->getValue();

    return ($name === NULL || $name === '')
      && ($school === NULL || $school === '')
      && ($telephone === NULL || $telephone === '')
      && ($role === NULL || $role === '')
      && ($email === NULL || $email === '');
  }
}
