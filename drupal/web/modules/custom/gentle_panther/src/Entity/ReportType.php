<?php

namespace Drupal\gentle_panther\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Report type entity.
 *
 * @ConfigEntityType(
 *   id = "report_type",
 *   label = @Translation("Report type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\gentle_panther\ReportTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\gentle_panther\Form\ReportTypeForm",
 *       "edit" = "Drupal\gentle_panther\Form\ReportTypeForm",
 *       "delete" = "Drupal\gentle_panther\Form\ReportTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\gentle_panther\ReportTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "report_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "report",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/report_type/{report_type}",
 *     "add-form" = "/admin/structure/report_type/add",
 *     "edit-form" = "/admin/structure/report_type/{report_type}/edit",
 *     "delete-form" = "/admin/structure/report_type/{report_type}/delete",
 *     "collection" = "/admin/structure/report_type"
 *   }
 * )
 */
class ReportType extends ConfigEntityBundleBase implements ReportTypeInterface {

  /**
   * The Report type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Report type label.
   *
   * @var string
   */
  protected $label;

}
