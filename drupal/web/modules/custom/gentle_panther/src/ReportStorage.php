<?php

namespace Drupal\gentle_panther;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\gentle_panther\Entity\ReportInterface;

/**
 * Defines the storage handler class for Report entities.
 *
 * This extends the base storage class, adding required special handling for
 * Report entities.
 *
 * @ingroup gentle_panther
 */
class ReportStorage extends SqlContentEntityStorage implements ReportStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ReportInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {report_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {report_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ReportInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {report_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('report_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
