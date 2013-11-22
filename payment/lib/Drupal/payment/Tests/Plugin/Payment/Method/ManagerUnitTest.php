<?php

/**
 * @file
 * Contains class \Drupal\payment\Tests\Plugin\Payment\Method\ManagerUnitTest.
 */

namespace Drupal\payment\Tests\Plugin\Payment\Method;

use Drupal\payment\Payment;
use Drupal\simpletest\DrupalUnitTestBase;

/**
 * Tests \Drupal\payment\Plugin\Payment\Method\Manager.
 */
class ManagerUnitTest extends DrupalUnitTestBase {

  /**
   * The payment method plugin manager.
   *
   * @var \Drupal\payment\Plugin\Payment\Method\Manager
   */
  public $manager;

  /**
   * {@inheritdoc}
   */
  public static $modules = array('payment');

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\payment\Plugin\Payment\Method\Manager unit test',
      'group' => 'Payment',
    );
  }

  /**
   * {@inheritdoc
   */
  protected function setUp() {
    parent::setUp();
    $this->manager = Payment::methodManager();
  }

  /**
   * Tests getDefinitions().
   */
  protected function testGetDefinitions() {
    // Test the default payment method plugins.
    $definitions = $this->manager->getDefinitions();
    $this->assertEqual(count($definitions), 2);
    foreach ($definitions as $definition) {
      $this->assertIdentical(strpos($definition['id'], 'payment_'), 0);
      $this->assertTrue(is_subclass_of($definition['class'], '\Drupal\payment\Plugin\Payment\Method\PaymentMethodInterface'));
    }
  }

  /**
   * Tests createInstance().
   */
  protected function testCreateInstance() {
    $id = 'payment_unavailable';
    $this->assertEqual($this->manager->createInstance($id)->getPluginId(), $id);
    $this->assertEqual($this->manager->createInstance('ThisIdDoesNotExist')->getPluginId(), $id);
  }

  /**
   * Tests options().
   */
  protected function testOptions() {
    $options = $this->manager->options();
    $this->assertTrue(strlen($options['payment_basic']));
  }
}