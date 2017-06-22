<?php

namespace Drupal\alloy_blog\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Blog entities.
 *
 * @ingroup alloy_blog
 */
interface BlogInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Blog title.
   *
   * @return string
   *   Name of the Blog.
   */
  public function getTitle();

  /**
   * Sets the Blog title.
   *
   * @param string $title
   *   The Blog title.
   *
   * @return \Drupal\alloy_blog\Entity\BlogInterface
   *   The called Blog entity.
   */
  public function setTitle($title);

  /**
   * Gets the Blog creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Blog.
   */
  public function getCreatedTime();

  /**
   * Sets the Blog creation timestamp.
   *
   * @param int $timestamp
   *   The Blog creation timestamp.
   *
   * @return \Drupal\alloy_blog\Entity\BlogInterface
   *   The called Blog entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Blog published status indicator.
   *
   * Unpublished Blog are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Blog is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Blog.
   *
   * @param bool $published
   *   TRUE to set this Blog to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\alloy_blog\Entity\BlogInterface
   *   The called Blog entity.
   */
  public function setPublished($published);

  /**
   * Gets the Blog revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Blog revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\alloy_blog\Entity\BlogInterface
   *   The called Blog entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Blog revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Blog revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\alloy_blog\Entity\BlogInterface
   *   The called Blog entity.
   */
  public function setRevisionUserId($uid);

}
