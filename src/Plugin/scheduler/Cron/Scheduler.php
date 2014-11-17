<?php

/**
 * @file
 * Contains \Drupal\scheduler\Plugin\scheduler\Cron\Scheduler.
 */

namespace Drupal\scheduler\Plugin\scheduler\Cron;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\node\NodeInterface;
use Drupal\scheduler\SchedulerCronPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides default plugin actions for scheduler.
 *
 * @SchedulerCron(
 *   id = "scheduler",
 *   label = @Translation("Default scheduler cron"),
 *   description = @Translation("Default scheduler cron.")
 * )
 */
class Scheduler extends SchedulerCronPluginBase {

  /**
   * The entity query object.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Constructs a Drupal\scheduler\Plugin\scheduler\Cron\scheduler.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The factory for query objects.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, QueryFactory $query_factory, DateFormatter $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory);
    $this->queryFactory = $query_factory;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity.query'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPublishedNodes() {
    return $this->queryFactory->get('node', 'AND')
      ->condition('publish_on', REQUEST_TIME, '<=')
      ->exists('publish_on')
      ->condition('status', 0)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getUnPublishedNodes() {
    return $this->queryFactory->get('node', 'AND')
      ->condition('unpublish_on', REQUEST_TIME, '<=')
      ->exists('unpublish_on')
      ->condition('status', 1)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function allowUnPublishing(NodeInterface $node) {
    // Do not process the node if it has a publish_on time which is in the past,
    // as this implies that scheduled publishing has been blocked by one of the
    // API functions we provide. Hence unpublishing should be halted too.
    $published_on = $node->get('publish_on')->value;
    return !empty($published_on) && $published_on <= REQUEST_TIME;
  }

  /**
   * {@inheritdoc}
   */
  public function prePublish(NodeInterface $node) {
    // Update timestamps.
    $published_on = $node->get('publish_on')->value;
    $node->set('changed', $published_on);
    $old_creation_date = $node->getCreatedTime();
    if ($this->config->get('publish_touch_' . $node->getType(), 0)) {
      $node->setCreatedTime($published_on);
    }

    if ($this->config->get('publish_revision_' . $node->getType(), 0)) {
      $node->setNewRevision();
      // Use a core date format to guarantee a time is included.
      $node->set('revision_log', t('Node published by Scheduler on @now. Previous creation date was @date.', [
        '@now' => $this->dateFormatter->format(REQUEST_TIME, 'short'),
        '@date' => $this->dateFormatter->format($old_creation_date, 'short'),
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preUnPublish(NodeInterface $node) {
    // Update timestamps.
    $old_change_date = $node->getChangedTime();
    $node->set('changed', $node->get('unpublish_on')->value);

    if ($this->config->get('unpublish_revision_' . $node->getType(), 0)) {
      $node->setNewRevision();
      // Use a core date format to guarantee a time is included.
      $node->set('revision_log', t('Node unpublished by Scheduler on @now. Previous change date was @date.', [
        '@now' => $this->dateFormatter->format(REQUEST_TIME, 'short'),
        '@date' => $this->dateFormatter->format($old_change_date, 'short'),
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function publishImmediately(NodeInterface $node) {
    if ($this->config->get('publish_touch_' . $node->getType(), 0)) {
      $node->setCreatedTime($node->get('publish_on')->value);
    }
  }
}
