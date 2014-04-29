<?php

/**
 * @file
 * Contains \Drupal\payment\Entity\PaymentStatus\PaymentStatusDeleteForm.
 */

namespace Drupal\payment\Entity\PaymentStatus;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the payments status deletion form.
 */
class PaymentStatusDeleteForm extends EntityConfirmFormBase {

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   */
  public function __construct(UrlGeneratorInterface $url_generator) {
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('url_generator'));
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you really want to delete %label?', array(
      '%label' => $this->getEntity()->label(),
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'payment.payment_status.list',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->getEntity()->delete();
    drupal_set_message(t('%label has been deleted.', array(
      '%label' => $this->getEntity()->label(),
    )));
    $form_state['redirect_route'] = array(
      'route_name' => 'payment.payment_status.list',
    );
  }
}
