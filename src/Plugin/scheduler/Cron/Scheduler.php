<?php

/**
 * @file
 * Contains \Drupal\scheduler\Plugin\scheduler\Cron\Scheduler.
 */

namespace Drupal\scheduler\Plugin\scheduler\Cron;

use Drupal\scheduler\SchedulerPluginBase;
use Drupal\node\NodeInterface;

/**
 * Provides default plugin actions for scheduler.
 *
 * @SchedulerCron(
 *   id = "scheduler",
 *   label = @Translation("Default scheduler cron"),
 *   description = @Translation("Default scheduler cron.")
 * )
 */
class Scheduler extends SchedulerPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPublishedNodes() {
    // @todo inject?
    return \Drupal::entityQuery('node')
      ->condition('publish_on', REQUEST_TIME, '<=')
      ->exists('publish_on')
      ->condition('status', 0)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getUnPublishedNodes() {
    // @todo inject?
    return \Drupal::entityQuery('node')
      ->condition('unpublish_on', REQUEST_TIME, '<=')
      ->exists('unpublish_on')
      ->condition('status', 1)
      ->execute();
  }

  public function allowUnPublishing(NodeInterface $node) {
    // Do not process the node if it has a publish_on time which is in the past,
    // as this implies that scheduled publishing has been blocked by one of the
    // API functions we provide. Hence unpublishing should be halted too.
    $published_on = $node->get('publish_on')->value;
    return !empty($published_on) && $published_on <= REQUEST_TIME;
  }

  public function prePublish(NodeInterface $node) {
    // Update timestamps.
    $published_on = $node->get('publish_on')->value;
    $node->set('changed', $published_on);
    $old_creation_date = $node->getCreatedTime();
    if ($this->config->get('scheduler_publish_touch_' . $node->getType(), 0)) {
      $node->setCreatedTime($published_on);
    }

    if ($this->config->get('scheduler_publish_revision_' . $node->getType(), 0)) {
      $node->setNewRevision();
      // Use a core date format to guarantee a time is included.
      // @todo inject?
      $node->set('revision_log', t('Node published by Scheduler on @now. Previous creation date was @date.', [
        '@now' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'short'),
        '@date' => \Drupal::service('date.formatter')->format($old_creation_date, 'short'),
      ]));
    }
  }

  public function preUnPublish(NodeInterface $node) {
    // Update timestamps.
    $old_change_date = $node->getChangedTime();
    $node->set('changed', $node->get('unpublish_on')->value);

    if ($this->config->get('scheduler_unpublish_revision_' . $node->getType(), 0)) {
      $node->setNewRevision();
      // Use a core date format to guarantee a time is included.
      // @todo inject?
      $node->set('revision_log', t('Node unpublished by Scheduler on @now. Previous change date was @date.', [
        '@now' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'short'),
        '@date' => \Drupal::service('date.formatter')->format($old_change_date, 'short'),
      ]));
    }
  }
}
