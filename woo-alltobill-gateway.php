<?php
/**
 * Plugin Name: Woo Alltobill Gateway
 * Description: Accept many different payment methods on your store using Alltobill
 * Author: Alltobill
 * Author URI: https://alltobill.com
 * Version: 1.6.0
 */

global $wpdb;

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

add_action('plugins_loaded', 'wc_offline_gateway_init', 11);

function wc_offline_gateway_init()
{
    class WC_Alltobill_Gateway extends WC_Payment_Gateway
    {
        public $enabled;
        public $title;
        public $instance;
        public $sid;
        public $prefix;
        public $logos;

        public function __construct()
        {
            $this->id = 'alltobill';
            $this->init_form_fields();
            $this->init_settings();

            $this->enabled = $this->get_option('enabled');
            $this->title = $this->get_option('title');
            $this->instance = $this->get_option('instance');
            $this->sid = $this->get_option('sid');
            $this->prefix = $this->get_option('prefix');
            $this->logos = $this->get_option('logos');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_api_wc_alltobill_gateway', array($this, 'check_webhook_response'));
        }

        public function get_icon()
        {
            $style = version_compare(WC()->version, '2.6', '>=') ? 'style="margin-left: 0.3em"' : '';

            $icon = '';
            foreach ($this->logos as $logo) {
                $icon .= '<img src="' . WC_HTTPS::force_https_url(plugins_url('cardicons/card_' . $logo . '.svg', __FILE__)) . '" alt="' . $logo . '" width="32" ' . $style . ' />';
            }

            return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
        }

        /**
         * Initialize Gateway Settings Form Fields
         */
        public function init_form_fields()
        {
            $this->form_fields = include('includes/settings-alltobill.php');
        }

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_alltobill_gateway($order)
            );
        }

        public function payment_scripts()
        {
            if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order']) && !is_add_payment_method_page()) {
                return;
            }
        }

        public function check_webhook_response()
        {
            $resp = $_REQUEST;
            $orderId = $resp['transaction']['invoice']['referenceId'];

            if (strpos($orderId, '_') !== false) {
                list($prefix, $orderId) = explode('_', $orderId);
            }
            if (!empty($prefix) && $this->prefix != $prefix) {
                return;
            }
            if (!isset($resp['transaction']['status'])) {
                return;
            }

            $status = $this->get_alltobill_status($orderId);
            if ($status !== $resp['transaction']['status']) {
                return;
            }

            $order = new WC_Order($orderId);
            if ($status == 'waiting' && in_array($resp['transaction']['psp'], array('PrePayment', 'Invoice'))) {
                // check for manual psp
                if ($order->status != 'on-hold') {
                    $order->update_status('on-hold', __('Awaiting offline payment', 'wc-alltobill-gateway'));
                }
            }
            if ($status == 'confirmed') {
                $order->payment_complete();
                // Reduce stock levels
                $order->reduce_order_stock();
                // Remove cart
                WC()->cart->empty_cart();
            }
        }

        public function get_alltobill_gateway($order_id)
        {
            global $wp;
            $order = new WC_Order($order_id);
            $amount = floatval($order->get_total());
            $currency = get_woocommerce_currency();

            $customer = new WC_Customer($order->id);
            $postcode = $customer->postcode;
            $city = $customer->city;
            $address_1 = $customer->address_1;
            $country = $customer->country;
            $first_name = $order->billing_first_name;
            $last_name = $order->billing_last_name;
            $company = $order->billing_company;
            $phone = $order->billing_phone;
            $email = $order->billing_email;

            spl_autoload_register(function ($class) {
                $root = __DIR__ . '/alltobill-php-master';
                $classFile = $root . '/lib/' . str_replace('\\', '/', $class) . '.php';
                if (file_exists($classFile)) {
                    require_once $classFile;
                }
            });
            // $instanceName is a part of the url where you access your alltobill installation.
            //https://{$instanceName}.alltobill.com
            $settings = get_option("woocommerce_alltobill_settings");
            // $secret is the alltobill secret for the communication between the applications
            // if you think someone got your secret, just regenerate it in the alltobill administration
            $instanceName = $settings['instance'];
            $secret = $settings['sid'];
            $alltobill = new \Alltobill\Alltobill($instanceName, $secret);

            $gateway = new \Alltobill\Models\Request\Gateway();

            $am = $amount;
            $gateway->setAmount($am * 100);

            if ($currency == "") {
                $currency = "USD";
            }
            $gateway->setCurrency($currency);

            $gateway->setSuccessRedirectUrl($this->get_return_url($order));
            $gateway->setFailedRedirectUrl(get_home_url());
            $gateway->setPsp(array());

            if ($this->prefix == "") {
                $gateway->setReferenceId($order->id);
            } else {
                $gateway->setReferenceId($this->prefix . '_' . $order->id);
            }

            $gateway->addField($type = 'title', $value = '');
            $gateway->addField($type = 'forename', $value = $first_name);
            $gateway->addField($type = 'surname', $value = $last_name);
            $gateway->addField($type = 'company', $value = $company);
            $gateway->addField($type = 'street', $value = $address_1);
            $gateway->addField($type = 'postcode', $value = $postcode);
            $gateway->addField($type = 'place', $value = $city);
            $gateway->addField($type = 'country', $value = $country);
            $gateway->addField($type = 'phone', $value = $phone);
            $gateway->addField($type = 'email', $value = $email);
            $gateway->addField($type = 'custom_field_1', $value = $order->id, $name = 'WooCommerce ID');

            try {
                $response = $alltobill->create($gateway);
                $order->update_meta_data('alltobill_gateway_id', $response->getId());
                $order->save();

                $language = substr(get_locale(), 0, 2);
                $res = 'https://' . $instanceName . '.alltobill.com/' . $language . '/?payment=' . $response->getHash();
                return $res;
            } catch (\Alltobill\AlltobillException $e) {
                print $e->getMessage();
            }
        }

        public function get_alltobill_status($order_id)
        {
            $order = new WC_Order($order_id);
            $gatewayId = $order->get_meta('alltobill_gateway_id', true);

            spl_autoload_register(function ($class) {
                $root = __DIR__ . '/alltobill-php-master';
                $classFile = $root . '/lib/' . str_replace('\\', '/', $class) . '.php';
                if (file_exists($classFile)) {
                    require_once $classFile;
                }
            });

            $settings = get_option("woocommerce_alltobill_settings");
            $instanceName = $settings['instance'];
            $secret = $settings['sid'];
            $alltobill = new \Alltobill\Alltobill($instanceName, $secret);

            $gateway = new \Alltobill\Models\Request\Gateway();
            $gateway->setId($gatewayId);
            try {
                $response = $alltobill->getOne($gateway);
                return $response->getStatus();
            } catch (\Alltobill\AlltobillException $e) {
                print $e->getMessage();
            }
        }
    }
}

function wc_alltobill_add_to_gateways($gateways)
{
    $gateways[] = 'WC_Alltobill_Gateway';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'wc_alltobill_add_to_gateways');
?>
