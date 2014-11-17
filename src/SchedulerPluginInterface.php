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
   * Gets list of nodes to be published.
   *
   * @return array
   *   Associative array with nid as key and value.
   */
  public function getPublishedNodes();

  /**
   * Gets list of nodes to be unpublished.
   *
   * @return array
   *   Associative array with nid as key and value.
   */
  public function getUnPublishedNodes();

  /**
   * Checks to see if the given node can be published.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   *
   * @return bool
   *   True if publishing is allowed. False otherwise.
   */
  public function allowPublishing(NodeInterface $node);

  /**
   * Checks to see if the given node can be unpublished.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   *
   * @return bool
   *   True if unpublishing is allowed. False otherwise.
   */
  public function allowUnPublishing(NodeInterface $node);

  /**
   * Perform updates to the node before publishing.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   */
  public function prePublish(NodeInterface $node);

  /**
   * Perform updates to the node after publishing.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   */
  public function publish(NodeInterface $node);

  /**
   * Perform updates to the node before unpublishing.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   */
  public function preUnPublish(NodeInterface $node);

  /**
   * Perform updates to the node after unpublishing.
   *
   * @param \Drupal\node\NodeInterface $node
   *   A node object.
   */
  public function unPublish(NodeInterface $node);

}
