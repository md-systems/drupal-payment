<?php

/**
 * @file
 * Contains functions for generating content and configuration.
 */

/**
 * Content and configuration generation.
 */
class PaymentGenerate {

  /**
   * Generate a payment method.
   *
   * @param string $controller_class_name
   * @param integer $uid
   *
   * @return PaymentMethod
   */
  static function paymentMethod($controller_class_name = 'PaymentMethodControllerUnavailable', $uid = 1) {
    require_once './modules/simpletest/drupal_web_test_case.php';
    $name = DrupalTestCase::randomName();
    $controller = \Drupal::service('plugin.manager.payment.payment_method')->createInstance($controller_class_name);
    $payment_method = new PaymentMethod(array(
      'controller' => $controller,
      'controller_data' => $controller->controller_data_defaults,
      'name' => strtolower($name),
      'title_generic' => strtoupper($name) . '-general',
      'title_specific' => strtoupper($name) . '-specific',
      'uid' => $uid,
    ));

    return $payment_method;
  }

  /**
   * Generate a payment.
   *
   * @param PaymentMethod $payment_method
   * @param integer $uid
   *
   * @return Payment
   */
  static function payment(PaymentMethod $payment_method, $uid = 1) {
    $payment = new Payment(array(
      'currency_code' => 'XXX',
      'description' => 'This is the payment description',
      'method' => $payment_method,
      'uid' => $uid,
    ));
    $payment->setLineItem(new PaymentLineItem(array(
      'amount' => 1.23,
      'description' => 'This is the line item description',
      'name' => 'payment-generate-1',
      'quantity' => 3,
    )));
    $payment->setLineItem(new PaymentLineItem(array(
      'amount' => 4.56,
      'description' => 'This is the line item description',
      'name' => 'payment-generate-2',
      'tax_rate' => 0.78,
    )));

    return $payment;
  }
}