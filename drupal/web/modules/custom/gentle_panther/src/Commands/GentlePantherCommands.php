<?php

namespace Drupal\gentle_panther\Commands;

use Drush\Commands\DrushCommands;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\hcpss_school_vocabulary\Commands\HcpssSchoolVocabularyCommands;
use Drupal\gentle_panther\Entity\Report;
use Drupal\gentle_panther\Generator\BullyingGenerator;

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
    $generator = new BullyingGenerator();
    
    $generator->deleteAll();
    
    for ($i = 0; $i < 250; $i++) {
      $generator->generate();
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
      'Related to the student’s perceived sexual orientationRelated to the student’s perceived sexual orientation',
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
   * Initialize the gentle-panther site.
   *
   * @command gentle-panther:init
   * @usage gentle-panther:init
   *   Initialize the site.
   */
  public function init() {
    $page = Node::create([
      'type' => 'page',
      'title' => 'Bullying/Harassment',
      'uid' => 1,
      'body' => [
        'format' => 'basic_html',
        'summary' => '',
        'value' => '
          <p>HCPSS has taken a strong stand against bullying with a goal to
          eradicate bullying. No act of bullying in Howard County schools will
          be ignored. Unfortunately, bullying is a reality that lives within the
          hallways of our schools and one that we must root out once and for
          all. We know that those who are bullied may experience depression,
          anxiety, sadness and loneliness. They can suffer from changes in sleep
          and eating patterns and loss of interest in activities that they
          typically enjoy. Children who have suffered through bullying have gone
          so far as to injure themselves and even take their own life.</p>

          <p>In October 2013 the State of Maryland enacted Grace’s Law, making
          repeated, malicious cyber-abuse of a minor a criminal offense.
          Cyberbullying is now against the law and violators can be fined,
          jailed or both. This law was championed by Christine McComas who has
          been a tireless proponent of anti-bullying efforts. Christine’s
          daughter, Grace, tragically succumbed to the mental anguish caused by
          cyberbullying and took her own life.</p>

          <p>If you are experiencing bullying, say something. If you have
          witnessed bullying, say something! Find a trusted adult such as a
          parent, teacher, guidance counselor, coach or mentor. You may also
          report bullying incidents online by using the form provided below.</p>
        ',
      ],
    ]);

    $page->save();

    \Drupal::configFactory()
      ->getEditable('system.site')
      ->set('page.front', '/node/' . $page->id())
      ->save();
    
    $this->seedBullyingDescriptors();
    $this->seedLocations();
    $this->seedRoles();
    
    $command = new HcpssSchoolVocabularyCommands();
    $command->import();
  }
}
