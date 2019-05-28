<?php

namespace Drupal\bullying\Service;

/**
 * A class for querying the HCPSS school api.
 */
class SchoolQueryService {

  /**
   * An array of all school data json decoded.
   *
   * @var array
   */
  private static $schools;

  /**
   * This is where the api is.
   *
   * @var string
   */
  private static $apiEndpoint = 'https://api.hocoschools.org';

  /**
   * Get the schools from the api.
   *
   * @return array
   */
  private static function getSchools() {
    if (empty(self::$schools)) {
      $json = file_get_contents(self::$apiEndpoint . '/schools.json');
      $schools = json_decode($json, true)['schools'];

      foreach ($schools as $acronyms) {
        foreach ($acronyms as $acronym) {
          $json = file_get_contents(self::$apiEndpoint . "/schools/{$acronym}.json");
          self::$schools[$acronym] = json_decode($json, true);
        }
      }
    }

    return self::$schools;
  }

  /**
   * Fetch a list of school names.
   *
   * @return array
   */
  public static function getNames() {
    $names = \Drupal::state()->get('hcpss_school_names');
    if (empty($names)) {
      $names = array_values(self::get(['full_name']));
      \Drupal::state()->set('hcpss_school_names', $names);
    }

    return $names;
  }

  /**
   * Get a list of schools where the key is the school acronym and the value
   * is the property specified by the $path param.
   *
   * For example, if you want a list of school phone numbers:
   * $query = new SchoolQuery();
   * $phoneNumbers = $query->get(['contact', 'phone']);
   *
   * $phoneNumbers contains an array like this:
   * Array(
   *   [arl] => 410-313-6998
   *   [cls] => 410-888-8800
   *   [hc] => 410-313-7081
   *   [aes] => 410-313-6853
   *   etc...
   *
   * @param array $path
   *   Each value in this array should be a key along the path to the property
   *   you are looking for.
   * @return array
   *   a list of schools where the key is the school acronym and the value
   *   is the property specified by the $path param.
   */
  public static function get(array $path) {
    $properties = [];
    foreach (self::getSchools() as $acronym => $school) {
      $property = $school;
      foreach ($path as $key) {
        $property = $property[$key];
      }

      $properties[$acronym] = $property;
    }

    return $properties;
  }
}
