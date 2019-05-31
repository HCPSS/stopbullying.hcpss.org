<?php

namespace Drupal\bullying\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Component\Utility\Random;
use Drupal\gentle_panther\Service\NameGeneratorService;

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
   * Get all taxonomy terms in the vocabulary.
   * 
   * @param string $vid
   * @return array
   */
  private static function getAllTermsOfType(string $vid) : array {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid, 0, NULL, TRUE);
    
    return $terms;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random  = new Random();
    $roles   = self::getAllTermsOfType('roles');
    $schools = self::getAllTermsOfType('schools');

    return [
      'name'      => NameGeneratorService::generate(),
      'email'     => $random->name() . '@example.com',
      'school'    => $schools[array_rand($schools, 1)]->id(),
      'telephone' => rand(pow(10, 8), pow(10, 9) - 1),
      'role'      => $roles[array_rand($roles, 1)]->id(),
    ];
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
        'school_id' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'default' => NULL,
          'not null' => FALSE,
        ],
        'role_id' => [
          'type' => 'int',
          'unsigned' => TRUE,
          'default' => NULL,
          'not null' => FALSE,
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
    $school    = $this->get('school_id')->getValue();
    $email     = $this->get('email')->getValue();
    $telephone = $this->get('telephone')->getValue();
    $role      = $this->get('role_id')->getValue();

    return ($name === NULL || $name === '')
      && ($school === NULL || $school === '')
      && ($telephone === NULL || $telephone === '')
      && ($role === NULL || $role === '')
      && ($email === NULL || $email === '');
  }
}
