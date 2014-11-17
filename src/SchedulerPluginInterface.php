<?php

/**
 * @file
 * Contains \Drupal\scheduler\SchedulerPluginInterface.
 */

namespace Drupal\scheduler;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\node\NodeInterface;

/**
 * Defines the interface for scheduler plugins.
 */
interface SchedulerPluginInterface extends PluginInspectionInterface {

  /**
   * Returns the display label.
   *
   * @return string
   *   The display label.
   */
  public function getPublishedNodes();

  /**
   * Gets list of fields provided by this plugin.
   *
   * @return array
   *   Associative array with field names as keys and descriptions as values.
   */
  public function getUnPublishedNodes();

  public function allowPublishing(NodeInterface $node);

  public function allowUnPublishing(NodeInterface $node);

  public function prePublish(NodeInterface $node);

  public function publish(NodeInterface $node);

  public function preUnPublish(NodeInterface $node);

  public function unPublish(NodeInterface $node);

}
