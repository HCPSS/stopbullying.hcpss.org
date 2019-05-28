<?php

namespace Drupal\bullying\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\bullying\Service\SchoolQueryService;

class ReportController extends ControllerBase {

  /**
   * Display the report form for the public.
   */
  public function report() {
    $node = Node::create(['type' => 'bullying_report']);

    $form = $this->entityFormBuilder()->getForm($node, 'public');

    return $form;
  }

  /**
   * Autocomplete callback for school names.
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function schools(Request $request) {
    $query = $request->query->get('q');
    $query = strtolower($query);

    if (strlen($query) < 3) {
      return new JsonResponse([]);
    }

    $schools = SchoolQueryService::getNames();
    $matches = array_filter($schools, function($school) use ($query) {
      $school = strtolower($school);

      return strpos($school, $query) !== FALSE;
    });

    $results = array_map(function ($match) {
      return [
        'value' => $match,
        'label' => $match,
      ];
    }, $matches);

    return new JsonResponse(array_values($results));
  }
}
