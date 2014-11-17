<?php
/**
 * @file
 * Contains \Drupal\Scheduler\SchedulerPluginBase
 */

namespace Drupal\scheduler;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\scheduler\SchedulerPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for scheduler plugins.
 */
abstract class SchedulerPluginBase extends PluginBase implements SchedulerPluginInterface, ContainerFactoryPluginInterface {
  /**
   * The scheduler.settings config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;


  /**
   * Constructs a Drupal\Scheduler\SchedulerPluginBase.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get('scheduler.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function allowPublishing(NodeInterface $node) {
    return TRUE;
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
