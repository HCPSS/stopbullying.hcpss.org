<?php

namespace Drupal\gentle_panther\Generator;

use Drupal\paragraphs\ParagraphInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\gentle_panther\Service\NameGeneratorService;
use Drupal\Component\Utility\Random;

class ReportGenerator {
  
  /**
   * @var Random
   */
  protected $random;
  
  public function __construct() {
    $this->random = new Random();
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
    $person = Paragraph::create([
      'type'          => 'person',
      'field_name'    => NameGeneratorService::generate(),
      'field_school'  => ['target_id' => $this->getRandomTermId('schools')],
      'field_student' => TRUE,
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
