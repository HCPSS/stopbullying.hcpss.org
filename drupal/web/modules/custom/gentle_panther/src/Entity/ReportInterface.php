<?php

namespace Drupal\gentle_panther\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Report entities.
 *
 * @ingroup gentle_panther
 */
interface ReportInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Report name.
   *
   * @return string
   *   Name of the Report.
   */
  public function getName();

  /**
   * Sets the Report name.
   *
   * @param string $name
   *   The Report name.
   *
   * @return \Drupal\gentle_panther\Entity\ReportInterface
   *   The called Report entity.
   */
  public function setName($name);

  /**
   * Gets the Report creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Report.
   */
  public function getCreatedTime();

  /**
   * Sets the Report creation timestamp.
   *
   * @param int $timestamp
   *   The Report creation timestamp.
   *
   * @return \Drupal\gentle_panther\Entity\ReportInterface
   *   The called Report entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Report revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Report revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\gentle_panther\Entity\ReportInterface
   *   The called Report entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Report revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Report revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\gentle_panther\Entity\ReportInterface
   *   The called Report entity.
   */
  public function setRevisionUserId($uid);

}
