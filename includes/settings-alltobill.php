<?php
return apply_filters('wc_offline_form_fields',
    array(
        'enabled' => array(
            'title' => __('Enable/Disable', 'wc-alltobill-gateway'),
            'type' => 'checkbox',
            'label' => __('Enable Alltobill', 'wc-alltobill-gateway'),
            'default' => 'no',
        ),
        'title' => array(
            'title' => __('Title  <small style="color: #ff0000;">(Required)</small>', 'woocommerce'),
            'type' => 'text',
            'custom_attributes' => array('required' => 'required'),
            'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
            'default' => __(get_option("woocommerce_alltobill_title"), 'woocommerce'),
            'desc_tip' => true,
        ),
        'instance' => array(
            'title' => __('Instance Name  <small style="color: #ff0000;">(Required)</small>', 'wc-alltobill-gateway'),
            'type' => 'text',
            'custom_attributes' => array('required' => 'required'),
            'description' => __('This controls the title for the payment method the customer sees during checkout.', 'wc-alltobill-gateway'),
            'default' => __(get_option("woocommerce_alltobill_instance"), 'wc-alltobill-gateway'),
            'desc_tip' => true,
        ),
        'sid' => array(
            'title' => __('Secret ID  <small style="color: #ff0000;">(Required)</small>', 'wc-alltobill-gateway'),
            'type' => 'text',
            'custom_attributes' => array('required' => 'required'),
            'description' => __('This controls the title for the payment method the customer sees during checkout.', 'wc-alltobill-gateway'),
            'default' => __(get_option("woocommerce_alltobill_sid"), 'wc-alltobill-gateway'),
            'desc_tip' => true,
        ),
        'prefix' => array(
            'title' => __('Prefix ', 'wc-alltobill-gateway'),
            'type' => 'text',
            'custom_attributes' => array(),
            'description' => __('This is necessary when you use more than one shop with only one alltobill instance.', 'wc-alltobill-gateway'),
            'default' => __(get_option("woocommerce_alltobill_prefix"), 'wc-alltobill-gateway'),
            'desc_tip' => true,
        ),
        'logos' => array(
            'title' => __('Select Logo  <small style="color: #ff0000;">(Required)</small>', 'wc-alltobill-gateway'),
            'type' => 'multiselect',
            'custom_attributes' => array('required' => 'required'),
            'description' => __('This controls the payment method logos the customer sees during checkout.', 'wc-alltobill-gateway'),
            'default' => __(get_option("woocommerce_alltobill_logos"), 'wc-alltobill-gateway'),
            'desc_tip' => true,
            'options' => array(
                'masterpass' => 'Masterpass',
                'mastercard' => 'Mastercard',
                'visa' => 'Visa',
                'apple_pay' => 'Apple Pay',
                'maestro' => 'Maestro',
                'jcb' => 'JCB',
                'american_express' => 'American Express',
                'wirpay' => 'WIRpay',
                'paypal' => 'PayPal',
                'bitcoin' => 'Bitcoin',
                'sofortueberweisung_de' => 'Sofort Überweisung',
                'airplus' => 'Airplus',
                'billpay' => 'Billpay',
                'bonuscard' => 'Bonus card',
                'cashu' => 'CashU',
                'cb' => 'Carte Bleue',
                'diners_club' => 'Diners Club',
                'direct_debit' => 'Direct Debit',
                'discover' => 'Discover',
                'elv' => 'ELV',
                'ideal' => 'iDEAL',
                'invoice' => 'Invoice',
                'myone' => 'My One',
                'paysafecard' => 'Paysafe Card',
                'postfinance_card' => 'PostFinance Card',
                'postfinance_efinance' => 'PostFinance E-Finance',
                'swissbilling' => 'SwissBilling',
                'twint' => 'TWINT',
            )
        ),
    )
);