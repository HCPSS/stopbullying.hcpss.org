<?php

namespace Drupal\gentle_panther;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Report entity.
 *
 * @see \Drupal\gentle_panther\Entity\Report.
 */
class ReportAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\gentle_panther\Entity\ReportInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view published report entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit report entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete report entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add report entities');
  }
}
