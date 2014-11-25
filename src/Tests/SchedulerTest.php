<?php


/**
 * @file
 * Contains \Drupal\scheduler\Tests\SchedulerTest.
 */

namespace Drupal\scheduler\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\node\Entity\NodeType;

/**
 * Base test class for scheduler tests.
 *
 * @group scheduler
 */
class SchedulerTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('scheduler', 'scheduler_test', 'options');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->container->get('entity.definition_update_manager')->applyUpdates();
  }

  /**
   * Tests scheduler_allow().
   */
  public function testAllowedPublishing() {

    // Create a content type programmaticaly.
    $type = NodeType::load('scheduler_test');

    $fields = array(
      'publish_enable' => 'scheduler_publish_enable',
      'publish_touch' => 'scheduler_publish_touch',
    );

    foreach ($fields as $setting => $field) {
      $type->setThirdPartySetting('scheduler', $setting, TRUE);
    }

    $type->save();
    // Create a node that is not approved for publication. Then simulate a cron
    // run, and check that the node is not published.
    $node = $this->createUnapprovedNode();
    scheduler_cron();
    $this->assertNodeNotPublished($node->id(), 'An unapproved node is not published after scheduling.');

    // Approve the node for publication, simulate a cron run, check that the
    // node is now published.
    $this->approveNode($node->id());
    scheduler_cron();
    $this->assertNodePublished($node->id(), 'An approved node is published after scheduling.');

    // Turn on immediate publication of nodes with publication dates in the past
    // and repeat the tests. It is not needed to simulate cron runs now.
    $type->setThirdPartySetting('scheduler', 'publish_past_date', 'publish');
    $type->save();

    $node = $this->createUnapprovedNode();
    $this->assertNodeNotPublished($node->id(), 'An unapproved node is not published immediately after saving.');
    $this->approveNode($node->id());
    $this->assertNodePublished($node->id(), 'An approved node is published immediately after saving.');
  }

  /**
   * Creates a new node that is not approved by the CEO.
   *
   * The node has a publication date in the past to make sure it will be
   * included in the next scheduling run.
   *
   * @return object
   *   A node object.
   */
  protected function createUnapprovedNode() {
    $settings = array(
      'status' => 0,
      'publish_on' => strtotime('-1 day'),
      'type' => 'scheduler_test',
      'scheduler_test_approved' => array(
        'value' => FALSE,
      ),
    );
    return $this->drupalCreateNode($settings);
  }

  /**
   * Approves a node for publication.
   *
   * @param int $nid
   *   The nid of the node to approve.
   */
  protected function approveNode($nid) {
    $node = entity_load('node', $nid, TRUE);
    $node->set('scheduler_test_approved', TRUE);
    $node->save();
  }

  /**
   * Check to see if a node is not published.
   *
   * @param int $nid
   *   The nid of the node to check.
   * @param string $message
   *   The message to display along with the assertion.
   * @param string $group
   *   The type of assertion - examples are "Browser", "PHP".
   *
   * @return bool
   *   TRUE if the assertion succeeded, FALSE otherwise.
   */
  protected function assertNodeNotPublished($nid, $message = NULL, $group = 'Other') {
    $message = $message ? $message : format_string('Node %nid is not published', array('%nid' => $nid));
    return $this->assertFalse($this->getPublicationStatus($nid), $message, $group);
  }

  /**
   * Check to see if a node is published.
   *
   * @param int $nid
   *   The nid of the node to check.
   * @param string $message
   *   The message to display along with the assertion.
   * @param string $group
   *   The type of assertion - examples are "Browser", "PHP".
   *
   * @return bool
   *   TRUE if the assertion succeeded, FALSE otherwise.
   */
  protected function assertNodePublished($nid, $message = NULL, $group = 'Other') {
    $message = $message ? $message : format_string('Node %nid is published', array('%nid' => $nid));
    return $this->assertTrue($this->getPublicationStatus($nid), $message, $group);
  }

  /**
   * Returns the publication status of a node.
   *
   * @param int $nid
   *   The nid of the node for which the publication status is desired.
   *
   * @return bool
   *   TRUE if the node is published, FALSE otherwise.
   */
  protected function getPublicationStatus($nid) {
    $node = entity_load('node', $nid, TRUE);
    return $node->isPublished();
  }

}
