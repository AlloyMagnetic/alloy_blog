<?php

namespace Drupal\alloy_blog;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class BlogStorage extends SqlContentEntityStorage implements BlogStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BlogInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {blog_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {blog_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BlogInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {blog_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('blog_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
