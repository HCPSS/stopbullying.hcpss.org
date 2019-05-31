<?php

namespace Drupal\gentle_panther\Service;

class NameGeneratorService {

  /**
   * Generate a name.
   *
   * @return string
   */
  public static function generate() {
    $first_names = [
      'Brendan', 'Matt', 'Vince', 'Brian', 'Emily', 'Joan', 'Brianna',
      'Allison', 'Jarrod', 'Mary', 'Nick', 'Anna', 'Debby', 'Jessica',
      'Priscilla', 'Daniel', 'David', 'James', 'Jeffrey', 'Kordell', 'Mark',
      'Michael', 'Michael', 'Shari', 'Susan', 'Tara', 'Jennifer',
    ];

    $last_names = [
      'Anderson', 'Barger', 'Dubay', 'Basset', 'Bahhar', 'Fox', 'Hartley',
      'Heddon', 'Thompson', 'Schiller', 'Griner', 'Gable', 'Summers',
      'Goldstein', 'Reaver', 'De Haven', 'Smith', 'McLean', 'Smith', 'Vaden',
      'Metzbower', 'Elprin', 'Tunstall', 'Matthews', 'Williams', 'Chiu', 'Rose',
    ];

    return vsprintf('%s %s', [
      $first_names[array_rand($first_names)],
      $last_names[array_rand($last_names)],
    ]);
  }
}
