<?php

use Automattic\Jetpack\Constants;

class Wp_Sheba_Gateway extends WC_Payment_Gateway
{
  /**
   * Constructor for the gateway.
   */
  public function __construct()
  {
    // Setup general properties.
    $this->setup_properties();

    // Load the settings.
    $this->init_form_fields();
    $this->init_settings();

    // Get settings.
    $this->title              = $this->get_option('title');
    $this->description        = $this->get_option('description');


    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
    add_filter('woocommerce_payment_complete_order_status', array($this, 'change_payment_complete_order_status'), 10, 3);

    // Customer Emails.
    add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
  }

  /**
   * Setup general properties for the gateway.
   */
  protected function setup_properties()
  {
    $this->id                 = 'Wp_Sheba_Gateway';
    $this->icon               = apply_filters('woocommerce_cod_icon', '');
    $this->method_title       = __('Sheba Payment', 'woocommerce');
    $this->method_description = __('Have your customers pay with Sheba code.', 'woocommerce');
    $this->has_fields         = false;
  }

  /**
   * Initialise Gateway Settings Form Fields.
   */
  public function init_form_fields()
  {
    $this->form_fields = array(
      'enabled'            => array(
        'title'       => __('Enable/Disable', 'woocommerce'),
        'label'       => __('Enable Sheba Payment', 'woocommerce'),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no',
      ),
      'title'              => array(
        'title'       => __('Title', 'woocommerce'),
        'type'        => 'text',
        'description' => __('Payment method description that the customer will see on your checkout.', 'woocommerce'),
        'default'     => __('Sheba Payment Gateway', 'woocommerce'),
        'desc_tip'    => true,
      ),
      'description'        => array(
        'title'       => __('Description', 'woocommerce'),
        'type'        => 'textarea',
        'description' => __('Payment method description that the customer will see on your website.', 'woocommerce'),
        'default'     => __('Pay with Sheba code.', 'woocommerce'),
        'desc_tip'    => true,
      ),
    );
  }

  /**
   * Check If The Gateway Is Available For Use.
   *
   * @return bool
   */
  public function is_available()
  {
    $amount = WC()->cart->get_total('edit');
    if($amount > 50000) {
      return true;
    }
    return false;
  }

  /**
   * Process the payment and return the result.
   *
   * @param int $order_id Order ID.
   * @return array
   */
  public function process_payment($order_id)
  {
    $order = wc_get_order($order_id);

    if ($order->get_total() > 0) {
      // Mark as processing or on-hold (payment won't be taken until delivery).
      $order->update_status(apply_filters('woocommerce_sheba_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order), __('Payment to be made upon delivery.', 'woocommerce'));
    } else {
      $order->payment_complete();
    }

    // Remove cart.
    WC()->cart->empty_cart();

    // Return thankyou redirect.
    return array(
      'result'   => 'success',
      'redirect' => $this->get_return_url($order),
    );
  }

  /**
   * Output for the order received page.
   */
  public function thankyou_page()
  {
    
      echo wp_kses_post(wpautop(wptexturize("thank you we will call you")));
    
  }

  /**
   * Change payment complete order status to completed for Sheba orders.
   *
   * @since  3.1.0
   * @param  string         $status Current order status.
   * @param  int            $order_id Order ID.
   * @param  WC_Order|false $order Order object.
   * @return string
   */
  public function change_payment_complete_order_status($status, $order_id = 0, $order = false)
  {
    if ($order && 'Wp_Sheba_Gateway' === $order->get_payment_method()) {
      $status = 'completed';
    }
    return $status;
  }
}

add_filter('woocommerce_payment_gateways', 'woocommerce_add_sheba_gateway');
function woocommerce_add_sheba_gateway($methods)
{
  $methods[] = 'Wp_Sheba_Gateway';
  return $methods;
}
