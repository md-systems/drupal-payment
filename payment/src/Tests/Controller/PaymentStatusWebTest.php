<?php

/**
 * @file
 * Contains \Drupal\payment\Tests\Controller\PaymentStatusWebTest.
 */

namespace Drupal\payment\Tests\Controller;

use Drupal\payment\Payment;
use Drupal\simpletest\WebTestBase ;

/**
 * Tests the payment status UI.
 */
class PaymentStatusWebTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('payment');

  /**
   * The payment status storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  public $paymentStatusStorage;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Payment status UI',
      'group' => 'Payment',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->paymentStatusStorage = \Drupal::entityManager()->getStorage('payment_status');
  }

  /**
   * Tests listing.
   */
  protected function testList() {
    $payment_status_id = strtolower($this->randomName());
    /** @var \Drupal\payment\Entity\PaymentStatusInterface $status */
    $status = $this->paymentStatusStorage->create(array());
    $status->setId($payment_status_id)
      ->setLabel($this->randomName())
      ->save();

    $path = 'admin/config/services/payment/status';
    $this->drupalGet($path);
    $this->assertResponse(403);
    $this->drupalLogin($this->drupalCreateUser(array('payment.payment_status.administer')));
    $this->drupalGet($path);
    $this->assertResponse(200);

    // Assert that the "Add payment status" link is visible.
    $this->assertLinkByHref('admin/config/services/payment/status/add');

    // Assert that all plugins are visible.
    $manager = Payment::statusManager();
    foreach ($manager->getDefinitions() as $definition) {
      $this->assertText($definition['label']);
      if ($definition['description']) {
        $this->assertText($definition['description']);
      }
    }

    // Assert that all config entity operations are visible.
    $this->assertLinkByHref('admin/config/services/payment/status/edit/' . $payment_status_id);
    $this->assertLinkByHref('admin/config/services/payment/status/delete/' . $payment_status_id);
  }

  /**
   * Tests adding and editing a payment status.
   */
  protected function testAdd() {
    $path = 'admin/config/services/payment/status/add';
    $this->drupalGet($path);
    $this->assertResponse(403);
    $this->drupalLogin($this->drupalCreateUser(array('payment.payment_status.administer')));
    $this->drupalGet($path);
    $this->assertResponse(200);

    // Test a valid submission.
    $payment_status_id = strtolower($this->randomName());
    $label = $this->randomString();
    $parent_id = 'payment_success';
    $description = $this->randomString();
    $this->drupalPostForm($path, array(
      'label' => $label,
      'id' => $payment_status_id,
      'parent_id' => $parent_id,
      'description' => $description,
    ), t('Save'));
    /** @var \Drupal\payment\Entity\PaymentStatusInterface $status */
    $status = $this->paymentStatusStorage->loadUnchanged($payment_status_id);
    if ($this->assertTrue((bool) $status)) {
      $this->assertEqual($status->id(), $payment_status_id);
      $this->assertEqual($status->label(), $label);
      $this->assertEqual($status->getParentId(), $parent_id);
      $this->assertEqual($status->getDescription(), $description);
    }

    // Test editing a payment status.
    $this->drupalGet('admin/config/services/payment/status/edit/' . $payment_status_id);
    $this->assertLinkByHref('admin/config/services/payment/status/delete/' . $payment_status_id);
    $label = $this->randomString();
    $parent_id = 'payment_success';
    $description = $this->randomString();
    $this->drupalPostForm(NULL, array(
      'label' => $label,
      'parent_id' => $parent_id,
      'description' => $description,
    ), t('Save'));
    /** @var \Drupal\payment\Entity\PaymentStatusInterface $status */
    $status = $this->paymentStatusStorage->loadUnchanged($payment_status_id);
    if ($this->assertTrue((bool) $status)) {
      $this->assertEqual($status->id(), $payment_status_id);
      $this->assertEqual($status->label(), $label);
      $this->assertEqual($status->getParentId(), $parent_id);
      $this->assertEqual($status->getDescription(), $description);
    }

    // Test an invalid submission.
    $this->drupalPostForm($path, array(
      'label' => $label,
      'id' => $payment_status_id,
    ), t('Save'));
    $this->assertFieldByXPath('//input[@id="edit-id" and contains(@class, "error")]');
  }

  /**
   * Tests deleting a payment status.
   */
  protected function testDelete() {
    $payment_status_id = strtolower($this->randomName());
    /** @var \Drupal\payment\Entity\PaymentStatusInterface $status */
    $status = $this->paymentStatusStorage->create(array());
    $status->setId($payment_status_id)
      ->save();

    $path = 'admin/config/services/payment/status/delete/' . $payment_status_id;
    $this->drupalGet($path);
    $this->assertResponse(403);
    $this->drupalLogin($this->drupalCreateUser(array('payment.payment_status.administer')));
    $this->drupalGet($path);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, array(), t('Delete'));
    $this->assertNull($this->paymentStatusStorage->loadUnchanged($payment_status_id));
  }
}
