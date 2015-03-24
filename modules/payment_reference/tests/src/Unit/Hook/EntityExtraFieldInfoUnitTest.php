<?php

/**
 * @file
 * Contains \Drupal\Tests\payment_reference\Unit\Hook\EntityExtraFieldInfoUnitTest.
 */

namespace Drupal\Tests\payment_reference\Unit\Hook;

use Drupal\payment_reference\Hook\EntityExtraFieldInfo;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\payment_reference\Hook\EntityExtraFieldInfo
 *
 * @group Payment
 */
class EntityExtraFieldInfoUnitTest extends UnitTestCase {

  /**
   * The service under test.
   *
   * @var \Drupal\payment_reference\Hook\EntityExtraFieldInfo
   */
  protected $service;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->stringTranslation = $this->getStringTranslationStub();

    $this->service = new EntityExtraFieldInfo($this->stringTranslation);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->service = new EntityExtraFieldInfo($this->stringTranslation);
  }

  /**
   * @covers ::invoke
   */
  public function testInvoke() {
    $fields = $this->service->invoke();
    $this->assertInternalType('array', $fields);
    $this->assertArrayHasKey('payment_method', $fields['payment']['payment_reference']['form']);
  }
}
