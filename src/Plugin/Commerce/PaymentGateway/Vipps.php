<?php

namespace Drupal\commerce_vipps\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Annotation\CommercePaymentGateway;
use Drupal\commerce_payment\PaymentMethodTypeManager;
use Drupal\commerce_payment\PaymentTypeManager;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\ClientInterface;
use Http\Adapter\Guzzle6\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides Vipps payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "vipps",
 *   label = "Vipps",
 *   display_label = "Vipps",
 *   forms = {
 *     "offsite-payment" = "Drupal\commerce_vipps\PluginForm\VippsForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa",
 *   },
 * )
 */
class Vipps extends OffsitePaymentGatewayBase implements VippsInterface {

  protected $api;

  protected $client;

  protected $adapter;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, PaymentTypeManager $payment_type_manager, PaymentMethodTypeManager $payment_method_type_manager, ClientInterface $client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $payment_type_manager, $payment_method_type_manager);

    $this->client = $client;
    $this->adapter = new Client($this->client);
    $this->api = new \Vipps\Vipps($this->adapter);
    $this->api->setMerchantID()->setMerchantSerialNumber()->setToken();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.commerce_payment_type'),
      $container->get('plugin.manager.commerce_payment_method_type'),
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'merchant_id' => '',
        'merchant_serial_number' => '',
        'token' => '',
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['merchant_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant ID'),
      '#default_value' => $this->configuration['merchant_id'],
      '#required' => TRUE,
    ];

    $form['merchant_serial_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant Serial Number'),
      '#default_value' => $this->configuration['merchant_serial_number'],
      '#required' => TRUE,
    ];

    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#default_value' => $this->configuration['token'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['merchant_id'] = $values['merchant_id'];
      $this->configuration['merchant_serial_number'] = $values['merchant_serial_number'];
      $this->configuration['token'] = $values['token'];
    }


//    $response = $this->doRequest([
//      'METHOD' => 'GetBalance',
//    ]);
//
//    if ($response['ACK'] != 'Success') {
//      $form_state->setError($form['api_username'], $this->t('Invalid API credentials.'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['merchant_id'] = $values['merchant_id'];
      $this->configuration['merchant_serial_number'] = $values['merchant_serial_number'];
      $this->configuration['token'] = $values['token'];
    }
  }

  public function onNotify(Request $request) {
    // TODO: Implement onNotify() method.
  }

  public function onReturn(OrderInterface $order, Request $request) {
    // TODO: Implement onReturn() method.
  }

  public function onCancel(OrderInterface $order, Request $request) {
    // TODO: Implement onCancel() method.
  }



}
