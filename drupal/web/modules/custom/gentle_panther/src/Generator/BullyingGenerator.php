<?php

namespace Drupal\gentle_panther\Generator;

use Drupal\gentle_panther\Entity\ReportInterface;
use Drupal\gentle_panther\Entity\Report;


class BullyingGenerator extends ReportGenerator {
  
  /**
   * Delete all reports.
   */
  public function deleteAll() {
    $rids = \Drupal::entityQuery('report')
      ->condition('type', 'bullying_harassment')
      ->execute();
    
    $reports = Report::loadMultiple($rids);
    
    foreach ($reports as $report) {
      $report->delete();
    }
  }
  
  /**
   * Generate a bullying report.
   * 
   * @return ReportInterface
   */
  public function generate() : ReportInterface {
    $bullying = Report::create([
      'type' => 'bullying_harassment',
      'field_description' => $this->random->paragraphs(rand(1, 3)),
    ]);
    
    if ($this->chance(85)) {
      $bullying->field_reporter = $this->generateReporter();
    }
    
    for ($i = rand(0, 3); $i > 0; $i--) {
      $bullying->field_targets[] = $this->generatePerson();
    }
    
    for ($i = rand(0, 3); $i > 0; $i--) {
      $bullying->field_witnesses[] = $this->generatePerson();
    }
    
    for ($i = rand(0, 3); $i > 0; $i--) {
      $bullying->field_offenders[] = $this->generatePerson();
    }
    
    for ($i = rand(0, 3); $i > 0; $i--) {
      $bullying->field_dates[] = $this->generateDate();
    }
    
    foreach ($this->getRandomTermIds('bullying_descriptors', rand(3, 6)) as $id) {
      $bullying->field_bullying_type[] = ['target_id' => $id];
    }
    
    foreach ($this->getRandomTermIds('locations', rand(1, 2)) as $id) {
      $bullying->field_locations[] = ['target_id' => $id];
    }
    
    if ($this->chance(25)) {
      $bullying->field_cause = $this->random->paragraphs(rand(1, 2));
    }
    
    if ($this->chance(50)) {
      $bullying->field_physical_injury = TRUE;
      $bullying->field_medical_attention = $this->chance(30);
      $bullying->field_permanent_effects = $this->chance(15);
    }
    
    if ($this->chance(50)) {
      $bullying->field_psychological_injury = TRUE;
      $bullying->field_psych_services_sought = $this->chance(25);
    }
    
    if ($this->chance(25)) {
      $bullying->field_caused_absences = TRUE;
      $bullying->field_days_absent = rand(1, 5);
    }
    
    if ($this->chance(25)) {
      $bullying->field_additional_information = $this->random->paragraphs(rand(1, 3));
    }
    
    $bullying->save();
    
    return $bullying;
  }
}
