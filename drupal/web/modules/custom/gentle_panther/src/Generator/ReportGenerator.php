<?php

namespace Drupal\gentle_panther\Generator;

use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\gentle_panther\Service\NameGeneratorService;
use Drupal\Component\Utility\Random;
use Drupal\gentle_panther\Entity\Report;
use Drupal\gentle_panther\Entity\ReportInterface;
use Drupal\Core\Field\FieldItemListInterface;

class ReportGenerator {

  /**
   * @var Random
   */
  protected $random;

  /**
   * @var string
   */
  protected $bundle;

  public function __construct(string $bundle) {
    $this->random = new Random();
    $this->bundle = $bundle;
  }

  /**
   * Delete all reports of the bundle type.
   */
  public function deleteAll() {
    $rids = \Drupal::entityQuery('report')
      ->condition('type', $this->bundle)
      ->execute();

    $reports = Report::loadMultiple($rids);

    foreach ($reports as $report) {
      $report->delete();
    }
  }

  /**
   * Generate the reports.
   *
   * @return ReportInterface
   */
  public function generate() : ReportInterface {
    $report = Report::create(['type' => $this->bundle]);

    foreach ($report->getFields() as $field) {
      $this->generateFieldValues($report, $field);
    }

    $report->save();

    return $report;
  }

  /**
   * Generate the value for the field.
   *
   * @param ReportInterface $report
   * @param FieldItemListInterface $field
   */
  protected function generateFieldValues(ReportInterface $report, FieldItemListInterface $field) {
    $skip_field = [
      'field_medical_attention',
      'field_permanent_effects',
      'field_psych_services_sought',
      'field_days_absent',
    ];

    if (in_array($field->getName(), $skip_field)) {
      return;
    }

    $person_fields = ['field_witnesses', 'field_targets', 'field_offenders'];
    if (in_array($field->getName(), $person_fields)) {
      for ($i = rand(0, 3); $i > 0; $i--) {
        $report->{$field->getName()} = $this->generatePerson();
      }
    }

    switch ($field->getName()) {
      case 'field_description':
        $report->field_description = $this->random->paragraphs(rand(1, 3));

        break;
      case 'field_reporter':
        if ($this->chance(85)) {
          $report->field_reporter = $this->generateReporter();
        }

        break;
      case 'field_dates':
        for ($i = rand(0, 3); $i > 0; $i--) {
          $report->field_dates[] = $this->generateDate();
        }

        break;
      case 'field_bullying_type':
        foreach ($this->getRandomTermIds('bullying_descriptors', rand(3, 6)) as $id) {
          $report->field_bullying_type[] = ['target_id' => $id];
        }

        break;
      case 'field_locations':
        foreach ($this->getRandomTermIds('locations', rand(1, 2)) as $id) {
          $report->field_locations[] = ['target_id' => $id];
        }

        break;
      case 'field_location':
        $report->field_location = $this->random->sentences(2);

        break;
      case 'field_cause':
        if ($this->chance(25)) {
          $report->field_cause = $this->random->paragraphs(rand(1, 2));
        }

        break;
      case 'field_physical_injury':
        $injury = $this->chance(50);
        $report->field_physical_injury = $injury;
        $report->field_medical_attention = $injury && $this->chance(30);
        $report->field_permanent_effects = $injury && $this->chance(15);

        break;
      case 'field_psychological_injury':
        $injury = $this->chance(50);
        $report->field_psychological_injury = $injury;
        $report->field_psych_services_sought = $injury && $this->chance(25);

        break;
      case 'field_caused_absences':
        $absences = $this->chance(25);
        $report->field_caused_absences = $absences;
        $report->field_days_absent = $absences ? rand(1, 5) : 0;

        break;
      case 'field_additional_information':
        if ($this->chance(25)) {
          $report->field_additional_information = $this->random->paragraphs(rand(1, 3));
        }

        break;
    }
  }

  /**
   * Has a $percent chance of returning true.
   *
   * @param int $percent
   * @return bool
   */
  protected function chance(int $percent) : bool {
    return rand(1, 100) <= $percent;
  }

  /**
   * Get a random term id.
   *
   * @param string $vocabulary
   * @return int
   */
  protected function getRandomTermId(string $vocabulary) : int {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vocabulary);

    $term = $terms[array_rand($terms)];

    return $term->tid;
  }

  /**
   * Get randome term ids.
   *
   * @param string $vocabulary
   * @param int $num_terms
   * @return array
   */
  protected function getRandomTermIds(string $vocabulary, int $num_terms) : array {
    $ids = [];

    while (count($ids) < $num_terms) {
      $id = $this->getRandomTermId($vocabulary);
      if (!in_array($id, $ids)) {
        $ids[] = $id;
      }
    }

    return $ids;
  }

  /**
   * Generate a random date.
   *
   * @return string
   */
  protected function generateDate() : string {
    return vsprintf('2018-%02d-%02d', [
      rand(1, 12),
      rand(1, 28),
    ]);
  }

  /**
   * Generate a person.
   *
   * @return ParagraphInterface
   */
  protected function generatePerson() : ParagraphInterface {
    $grades = array_merge(range(1, 12), ['pre-k', 'kindergarten']);

    $person = Paragraph::create([
      'type'         => 'person',
      'field_name'   => NameGeneratorService::generate(),
      'field_school' => ['target_id' => $this->getRandomTermId('schools')],
      'field_grade'  => $grades[array_rand($grades)],
    ]);

    $person->save();

    return $person;
  }

  /**
   * Generate a reporter.
   *
   * @return ParagraphInterface
   */
  protected function generateReporter() : ParagraphInterface {
    $name = NameGeneratorService::generate();

    $reporter = Paragraph::create([
      'type'         => 'reporter',
      'field_name'   => $name,
      'field_phone'  => rand(pow(10, 8), pow(10, 9) - 1),
      'field_email'  => str_replace(' ', '_', $name) . '@example.com',
      'field_school' => ['target_id' => $this->getRandomTermId('schools')],
      'field_role'   => ['target_id' => $this->getRandomTermId('roles')],
    ]);

    $reporter->save();

    return $reporter;
  }
}
