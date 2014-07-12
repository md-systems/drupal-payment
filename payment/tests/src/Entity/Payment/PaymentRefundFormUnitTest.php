<?php

/**
 * @file
 * Contains \Drupal\payment\Tests\Entity\Payment\PaymentRefundFormUnitTest.
 */

namespace Drupal\payment\Tests\Entity\Payment;

use Drupal\Core\Url;
use Drupal\payment\Entity\Payment\PaymentRefundForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\payment\Entity\Payment\PaymentRefundForm
 *
 * @group Payment
 */
class PaymentRefundFormUnitTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\payment\Entity\Payment\PaymentRefundForm
   */
  protected $form;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityManager;

  /**
   * The payment.
   *
   * @var \Drupal\payment\Entity\Payment|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $payment;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $this->payment = $this->getMockBuilder('\Drupal\payment\Entity\Payment')
      ->disableOriginalConstructor()
      ->getMock();

    $this->form = new PaymentRefundForm($this->entityManager, $this->stringTranslation);
    $this->form->setEntity($this->payment);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityManager),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = PaymentRefundForm::create($container);
    $this->assertInstanceOf('\Drupal\payment\Entity\Payment\PaymentRefundForm', $form);
  }

  /**
   * @covers ::getConfirmText
   */
  function testGetConfirmText() {
    $this->assertInternalType('string', $this->form->getConfirmText());
  }

  /**
   * @covers ::getQuestion
   */
  function testGetQuestion() {
    $this->assertInternalType('string', $this->form->getQuestion());
  }

  /**
   * @covers ::getCancelRoute
   */
  function testGetCancelRoute() {
    $url = new Url($this->randomName());

    $this->payment->expects($this->atLeastOnce())
      ->method('urlInfo')
      ->with('canonical')
      ->will($this->returnValue($url));

    $this->assertSame($url, $this->form->getCancelRoute());
  }

  /**
   * @covers ::submit
   */
  function testSubmit() {
    $payment_method = $this->getMock('\Drupal\payment\Plugin\Payment\Method\PaymentMethodRefundPaymentInterface');
    $payment_method->expects($this->once())
      ->method('refundPayment');

    $url = new Url($this->randomName());

    $this->payment->expects($this->atLeastOnce())
      ->method('getPaymentMethod')
      ->will($this->returnValue($payment_method));
    $this->payment->expects($this->atLeastOnce())
      ->method('urlInfo')
      ->with('canonical')
      ->will($this->returnValue($url));

    $form = array();
    $form_state = array();

    $this->form->submit($form, $form_state);
    $this->assertSame($url, $form_state['redirect_route']);
  }

}
