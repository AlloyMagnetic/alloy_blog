<?php

namespace Drupal\alloy_blog\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\alloy_blog\Entity\BlogInterface;

/**
 * Class BlogController.
 *
 *  Returns responses for Blog routes.
 *
 * @package Drupal\alloy_blog\Controller
 */
class BlogController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Blog revision.
   *
   * @param int $blog_revision
   *   The Blog  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($blog_revision) {
    $blog = $this->entityManager()->getStorage('blog')->loadRevision($blog_revision);
    $view_builder = $this->entityManager()->getViewBuilder('blog');

    return $view_builder->view($blog);
  }

  /**
   * Page title callback for a Blog revision.
   *
   * @param int $blog_revision
   *   The Blog  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($blog_revision) {
    $blog = $this->entityManager()->getStorage('blog')->loadRevision($blog_revision);
    return $this->t('Revision of %title from %date', ['%title' => $blog->label(), '%date' => format_date($blog->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Blog.
   *
   * @param \Drupal\alloy_blog\Entity\BlogInterface $blog
   *   A Blog  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BlogInterface $blog) {
    $account = $this->currentUser();
    $langcode = $blog->language()->getId();
    $langname = $blog->language()->getName();
    $languages = $blog->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $blog_storage = $this->entityManager()->getStorage('blog');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $blog->label()]) : $this->t('Revisions for %title', ['%title' => $blog->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all blog revisions") || $account->hasPermission('administer blog entities')));
    $delete_permission = (($account->hasPermission("delete all blog revisions") || $account->hasPermission('administer blog entities')));

    $rows = [];

    $vids = $blog_storage->revisionIds($blog);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\alloy_blog\BlogInterface $revision */
      $revision = $blog_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $blog->getRevisionId()) {
          $link = $this->l($date, new Url('entity.blog.revision', ['blog' => $blog->id(), 'blog_revision' => $vid]));
        }
        else {
          $link = $blog->link($date);
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
              Url::fromRoute('entity.blog.translation_revert', ['blog' => $blog->id(), 'blog_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.blog.revision_revert', ['blog' => $blog->id(), 'blog_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.blog.revision_delete', ['blog' => $blog->id(), 'blog_revision' => $vid]),
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

    $build['blog_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
