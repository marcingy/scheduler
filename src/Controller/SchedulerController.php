<?php
/**
 * @file
 * Contains \Drupal\scheduler\Controller\SchedulerController.
 */

namespace Drupal\scheduler\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Scheduler controller.
 */
class SchedulerController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Constructs a SchedulerController object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   The route..
   */
  public function __construct(RouteMatchInterface $route) {
    $this->currentRoute = $route;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function accessUID(AccountInterface $account) {
    return $account->id() == $this->currentRoute->getParameter('arg_0') || $account->hasPermission('view scheduled content') ? AccessResult::allowed() : AccessResult::neutral();
  }
}
