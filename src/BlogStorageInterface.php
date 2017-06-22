<?php

namespace Drupal\alloy_blog;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\alloy_blog\Entity\BlogInterface;

/**
 * Defines the storage handler class for Blog entities.
 *
 * This extends the base storage class, adding required special handling for
 * Blog entities.
 *
 * @ingroup alloy_blog
 */
interface BlogStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Blog revision IDs for a specific Blog.
   *
   * @param \Drupal\alloy_blog\Entity\BlogInterface $entity
   *   The Blog entity.
   *
   * @return int[]
   *   Blog revision IDs (in ascending order).
   */
  public function revisionIds(BlogInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Blog author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Blog revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\alloy_blog\Entity\BlogInterface $entity
   *   The Blog entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BlogInterface $entity);

  /**
   * Unsets the language for all Blog with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
