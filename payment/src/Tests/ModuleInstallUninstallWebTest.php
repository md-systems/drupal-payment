<?php

/**
 * @file
 * Contains \Drupal\payment\Tests\ModuleInstallUninstallWebTest.
 */

namespace Drupal\payment\Tests;

use Drupal\payment\Entity\PaymentMethodConfigurationInterface;
use Drupal\simpletest\WebTestBase;

/**
 * Tests module installation and uninstallation.
 */
class ModuleInstallUninstallWebTest extends WebTestBase {

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
      'name' => 'Module installation and uninstallation',
      'group' => 'Payment',
    );
  }

  /**
   * Test installation and uninstallation.
   */
  protected function testInstallationAndUninstallation() {
    $handler = \Drupal::moduleHandler();
    $this->assertTrue($handler->moduleExists('payment'));

    // Test default configuration.
    $names = array('collect_on_delivery', 'no_payment_required');
    foreach ($names as $name) {
      $payment_method = entity_load('payment_method_configuration', $name);
      $this->assertTrue($payment_method instanceof PaymentMethodConfigurationInterface);
    }

    $handler->uninstall(array('payment'));
    $this->assertFalse($handler->moduleExists('payment'));
  }
}