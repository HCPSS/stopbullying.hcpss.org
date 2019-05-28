<?php

namespace Drupal\bullying\Commands;

use Drush\Commands\DrushCommands;
use Drupal\node\Entity\Node;

/**
 * A drush command file.
 */
class BullyingCommands extends DrushCommands {

  /**
   * Initialize the bullying site.
   *
   * @command bullying:init
   * @usage bullying:init
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
  }
}
