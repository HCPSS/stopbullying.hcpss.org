<?php

namespace Drupal\gentle_panther;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ReportStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Report revision IDs for a specific Report.
   *
   * @param \Drupal\gentle_panther\Entity\ReportInterface $entity
   *   The Report entity.
   *
   * @return int[]
   *   Report revision IDs (in ascending order).
   */
  public function revisionIds(ReportInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Report author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Report revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\gentle_panther\Entity\ReportInterface $entity
   *   The Report entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ReportInterface $entity);

  /**
   * Unsets the language for all Report with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
