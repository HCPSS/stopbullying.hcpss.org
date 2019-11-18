<?php

namespace Drupal\gentle_panther\Commands;

use Drush\Commands\DrushCommands;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\hcpss_school_vocabulary\Commands\HcpssSchoolVocabularyCommands;
use Drupal\gentle_panther\Generator\ReportGenerator;
use Drupal\Component\Serialization\Yaml;
use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * A drush command file.
 */
class GentlePantherCommands extends DrushCommands {

  /**
   * Create fake reports.
   *
   * @command gentle-panther:generate:reports
   * @usage gentle-panther:generate:reports
   */
  public function generateReports() {
    $generators = [];
    $generators[] = new ReportGenerator('bullying_harassment');
    $generators[] = new ReportGenerator('inappropriate_conduct');
    $generators[] = new ReportGenerator('potential_altercation_threats');
    $generators[] = new ReportGenerator('substance_use_abuse');

    foreach ($generators as $generator) {
      $generator->deleteAll();

      for ($i = 0; $i < 250; $i++) {
        $generator->generate();
      }
    }
  }

  /**
   * Seed the locations.
   *
   * @command gentle-panther:seed:locations
   * @usage gentle-panther:seed:locations
   */
  public function seedLocations() {
    $locations = [
      'On school property' => TRUE,
      'At a school-sponsored activity or event off school property' => TRUE,
      'On a school bus' => TRUE,
      'On the way to/from school' => TRUE,
      'Via Internet–sent off school property' => FALSE,
      'Via Internet–sent on school property' => FALSE,
    ];

    foreach ($locations as $location => $physical) {
      Term::create([
        'vid' => 'locations',
        'name' => $location,
        'field_physical' => $physical,
      ])->save();
    }
  }

  /**
   * Seed the roles.
   *
   * @command gentle-panther:seed:roles
   * @usage gentle-panther:seed:roles
   */
  public function seedRoles() {
    $roles = [
      'Bystander',
      'Close adult relative of a student',
      'Parent/guardian of a student',
      'School Staff',
      'Student',
    ];

    foreach ($roles as $role) {
      Term::create([
        'vid' => 'roles',
        'name' => $role,
      ])->save();
    }
  }

  /**
   * Seed the bullying descriptors.
   *
   * @command gentle-panther:seed:bullying-descriptors
   * @usage gentle-panther:seed:bullying-descriptors
   */
  public function seedBullyingDescriptors() {
    $descriptors = [
      'Any bullying, harassment, or intimidation that involves physical aggression.',
      'Getting another person to hit or harm the student',
      'Teasing, name-calling, making critical remarks, or threatening, in person or by other means',
      'Demeaning and making the target/victim of jokes',
      'Making rude and/or threatening gestures',
      'Excluding or rejecting the student',
      'Intimidating, extorting, or exploiting',
      'Spreading harmful rumors or gossip',
      'Related to the student’s disability',
      'Related to the student’s perceived sexual orientation',
      'Electronic Communication (e.g. E-mail, text, sexting, etc.)',
      'Gang Related',
      'Gang Recruitment',
      'Human trafficking/prostitution recruitment',
      'Racial Harassment',
      'Sexual Discrimination (Harassment)',
      'Sexual in nature',
      'Cyberbullying (e.g. social media including Facebook, Twitter, Vine, Snapchat, Periscope, kik, Instagram, etc.)',
    ];

    foreach ($descriptors as $descriptor) {
      Term::create([
        'vid' => 'bullying_descriptors',
        'name' => $descriptor,
        'field_display' => TRUE,
      ])->save();
    }
  }

  /**
   * Seed pages.
   *
   * @command gentle-panther:seed:pages
   */
  public function seedPages() {
    $filepath = vsprintf('%s/%s/%s', [
      DRUPAL_ROOT,
      drupal_get_path('module', 'gentle_panther'),
      'data/pages.yml',
    ]);
    $yaml = file_get_contents($filepath);
    $pageData = Yaml::decode($yaml);

    foreach ($pageData as $index => $data) {
      $page = Node::create([
        'type' => 'page',
        'title' => $data['title'],
        'uid' => 1,
        'body' => [
          'format' => 'full_html',
          'summary' => '',
          'value' => $data['body'],
        ],
      ]);

      $page->save();

      if ($data['front']) {
        \Drupal::configFactory()
          ->getEditable('system.site')
          ->set('page.front', '/node/' . $page->id())
          ->save();
      }

      MenuLinkContent::create([
        'title' => $data['title'],
        'link' => ['uri' => 'entity:node/' . $page->id()],
        'menu_name' => 'main',
        'weight' => $index,
      ])->save();
    }
  }

  /**
   * Initialize the gentle-panther site.
   *
   * @command gentle-panther:init
   * @usage gentle-panther:init
   *   Initialize the site.
   */
  public function init() {
    $this->seedPages();
    $this->seedBullyingDescriptors();
    $this->seedLocations();
    $this->seedRoles();

    $command = new HcpssSchoolVocabularyCommands();
    $command->import();
  }
}
