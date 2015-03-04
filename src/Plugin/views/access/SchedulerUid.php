<?php

/**
 * @file
 * Definition of Drupal\scheduler\Plugin\views\access\SchedulerUid.
 */

namespace Drupal\scheduler\Plugin\views\access;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\PermissionHandlerInterface;
use Drupal\views\Plugin\views\access\AccessPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

/**
 * Access plugin that provides permission-based access control.
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "scheduler_uid",
 *   title = @Translation("Scheduler and uid"),
 *   help = @Translation("Access will be granted to users with the view scheduled content permission or matching UID.")
 * )
 */
class SchedulerUid extends AccessPluginBase {

  /**
   * The route.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRoute;

  /**
   * Constructs a Permission object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\user\PermissionHandlerInterface $permission_handler
   *   The permission handler.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   The route.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PermissionHandlerInterface $permission_handler, RouteMatchInterface $route) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $permission_handler);
    $this->currentRoute = $route;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('user.permissions'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return $account->hasPermission('view scheduled content') || ($account->id() == $this->currentRoute->getParameter('arg_0'));
  }

  /**
   * {@inheritdoc}
   */
  public function alterRouteDefinition(Route $route) {
    $route->setRequirement('_custom_access', 'Drupal\scheduler\Controller\SchedulerController::accessUID');
  }
}
