<?php

/**
 * @file
 * Contains \Drupal\scheduler_test\Plugin\scheduler\Cron\SchedulerTest.
 */

namespace Drupal\scheduler_test\Plugin\scheduler\Cron;

use Drupal\Component\Plugin\PluginBase;
use Drupal\node\NodeInterface;
use Drupal\scheduler\SchedulerCronPluginInterface;

/**
 * Provides test plugin actions for scheduler.
 *
 * @SchedulerCron(
 *   id = "scheduler_test",
 *   label = @Translation("Test scheduler cron"),
 *   description = @Translation("Test scheduler cron.")
 * )
 */
class SchedulerTest extends PluginBase implements SchedulerCronPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getPublishedNodes() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getUnPublishedNodes() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function allowUnPublishing(NodeInterface $node) {}

  /**
   * {@inheritdoc}
   */
  public function prePublish(NodeInterface $node) {}

  /**
   * {@inheritdoc}
   */
  public function preUnPublish(NodeInterface $node) {}

  /**
   * {@inheritdoc}
   */
  public function publishImmediately(NodeInterface $node) {}

  /**
   * {@inheritdoc}
   */
  public function allowPublishing(NodeInterface $node) {
    // Only publish nodes that have scheduler_test_approved set to TRUE.
    $approved = $node->get('scheduler_test_approved')->value;
    return !empty($approved);
  }

  /**
   * {@inheritdoc}
   */
  public function publish(NodeInterface $node) {}

  /**
   * {@inheritdoc}
   */
  public function unPublish(NodeInterface $node) {}
}
