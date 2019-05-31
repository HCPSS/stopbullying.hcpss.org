<?php

namespace Drupal\gentle_panther\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\gentle_panther\Entity\ReportInterface;
use Drupal\gentle_panther\Entity\Report;
use Consolidation\OutputFormatters\Formatters\FormatterInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Component\Utility\Random;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ReportController.
 *
 *  Returns responses for Report routes.
 */
class ReportController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * A page that helps people choose what type of report to make.
   * 
   * @return array
   */
  public function choose() {
    $random = new Random();
    
    $report_types = [
      [
        'route' => 'gentle_panther.report.bullying',
        'name' => 'Bullying and Harassment',
        'description' => $random->paragraphs(1),
      ],
      [
        'route' => 'gentle_panther.report.inappropriate_conduct',
        'name' => 'Inappropriate Conduct',
        'description' => $random->paragraphs(1),
      ],
      [
        'route' => 'gentle_panther.report.potential_altercation_threats',
        'name' => 'Potential Altercation/Threats',
        'description' => $random->paragraphs(1),
      ],
      [
        'route' => 'gentle_panther.report.substance_use_abuse',
        'name' => 'Substance Use/Abuse',
        'description' => $random->paragraphs(1),
      ],
    ];
    
    return [
      '#theme' => 'choose_report',
      '#report_types' => $report_types,
    ];
  }
  
  /**
   * Thanks page for submitting a report. 
   * 
   * @param string $tracking_number
   * @throws NotFoundHttpException
   * @return array
   *   Render array
   */
  public function thanks($tracking_number) {
    $rids = \Drupal::entityQuery('report')
      ->condition('name', $tracking_number)
      ->execute();
    
    if (empty($rids)) {
      throw new NotFoundHttpException();
    }
      
    return [
      '#markup' => '
        <p>Thank you for reporting this issue to us. Your report has been issued 
        tracking number ' . $tracking_number . '</p>

        <p>If you provided your contact details, you will be contacted within 72 
        hours regarding this issue.</p>
      ',
    ];
  }
  
  public function reportFormSubmit(array $form, FormStateInterface $form_state) {

    
    $form_state->setRedirect('gentle_panther.report.thanks', [
      'tracking_number' => 'fakenumber',
    ]);
  }
  
  /**
   * Get a report form by bundle.
   * 
   * @param string $report_bundle
   * @return array
   */
  private function reportForm(string $report_bundle) {
    $report = Report::create(['type' => $report_bundle]);
    $form = $this->entityFormBuilder()->getForm($report);
    
    if (array_key_exists('revision_log_message', $form)) {
      unset($form['revision_log_message']);
    }
    
    return $form;
  }
  
  /**
   * Public Substance Use/Abuse form.
   *
   * @return array
   *   Renderable form
   */
  public function publicSubstanceUseForm() {
    return $this->reportForm('substance_use_abuse');
  }
  
  /**
   * Public Potential Altercation / Threat form.
   *
   * @return array
   *   Renderable form
   */
  public function publicPotentialAltercationForm() {
    return $this->reportForm('potential_altercation_threats');
  }
  
  /**
   * Public Inappropriate Conduct form.
   *
   * @return array
   *   Renderable form
   */
  public function publicInappropriateConductForm() {
    return $this->reportForm('inappropriate_conduct');
  }
  
  /**
   * Public bullying form.
   * 
   * @return array
   *   Renderable form
   */
  public function publicBullyingForm() {
    return $this->reportForm('bullying_harassment');
  }
  
  /**
   * Displays a Report  revision.
   *
   * @param int $report_revision
   *   The Report  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($report_revision) {
    $report = $this->entityManager()->getStorage('report')->loadRevision($report_revision);
    $view_builder = $this->entityManager()->getViewBuilder('report');

    return $view_builder->view($report);
  }

  /**
   * Page title callback for a Report  revision.
   *
   * @param int $report_revision
   *   The Report  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($report_revision) {
    $report = $this->entityManager()->getStorage('report')->loadRevision($report_revision);
    return $this->t('Revision of %title from %date', ['%title' => $report->label(), '%date' => format_date($report->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Report .
   *
   * @param \Drupal\gentle_panther\Entity\ReportInterface $report
   *   A Report  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ReportInterface $report) {
    $account = $this->currentUser();
    $langcode = $report->language()->getId();
    $langname = $report->language()->getName();
    $languages = $report->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $report_storage = $this->entityManager()->getStorage('report');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $report->label()]) : $this->t('Revisions for %title', ['%title' => $report->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all report revisions") || $account->hasPermission('administer report entities')));
    $delete_permission = (($account->hasPermission("delete all report revisions") || $account->hasPermission('administer report entities')));

    $rows = [];

    $vids = $report_storage->revisionIds($report);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\gentle_panther\ReportInterface $revision */
      $revision = $report_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $report->getRevisionId()) {
          $link = $this->l($date, new Url('entity.report.revision', ['report' => $report->id(), 'report_revision' => $vid]));
        }
        else {
          $link = $report->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.report.translation_revert', ['report' => $report->id(), 'report_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.report.revision_revert', ['report' => $report->id(), 'report_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.report.revision_delete', ['report' => $report->id(), 'report_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['report_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
