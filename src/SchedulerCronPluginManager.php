<?php

/**
 * @file
 * Contains \Drupal\scheduler\SchedulerCronPluginManager.
 */

namespace Drupal\scheduler;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages scheduler plugins.
 */
class SchedulerCronPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new SchedulerPluginManagerCron.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/scheduler/Cron', $namespaces, $module_handler, 'Drupal\scheduler\SchedulerPluginInterface', 'Drupal\scheduler\Annotation\SchedulerCron');

    $this->alterInfo('scheduler_cron_plugins_info');
    $this->setCacheBackend($cache_backend, 'scheduler_cron_plugins');
  }

}
