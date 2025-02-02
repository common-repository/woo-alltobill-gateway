<?php

spl_autoload_register(function($class) {
    $root = dirname(__DIR__);
    $classFile = $root . '/lib/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// $instanceName is a part of the url where you access your alltobill installation.
// https://{$instanceName}.alltobill.com
$instanceName = 'YOUR_INSTANCE_NAME';

// $secret is the alltobill secret for the communication between the applications
// if you think someone got your secret, just regenerate it in the alltobill administration
$secret = 'YOUR_SECRET';

$alltobill = new \Alltobill\Alltobill($instanceName, $secret);

// init empty request object
$invoice = new \Alltobill\Models\Request\Invoice();

// info for payment link (reference id)
$invoice->setReferenceId('Order number of my online shop application');

// info for payment page (title, description)
$invoice->setTitle('Online shop payment');
$invoice->setDescription('Thanks for using Alltobill to pay your order');

// administrative information, which provider to use (psp)
// psp #1 = Alltobill' test mode, see http://developers.alltobill.com/docs/miscellaneous
$invoice->setPsp(1);

// internal data only displayed to administrator
$invoice->setName('Online-Shop payment #001');

// payment information
$invoice->setPurpose('Shop Order #001');
$amount = 5.90;
// don't forget to multiply by 100
$invoice->setAmount($amount * 100);
// ISO code of currency, list of alternatives can be found here
// http://developers.alltobill.com/docs/miscellaneous
$invoice->setCurrency('CHF');

// subscription information if you want the customer to authorize a recurring payment
// NOTE: This functionality is currently only available by using PAYMILL as a payment service provider
//$invoice->setSubscriptionState(true);
//$invoice->setSubscriptionInterval('P1M');
//$invoice->setSubscriptionPeriod('P1Y');
//$invoice->setSubscriptionCancellationInterval('P3M');

// add contact information fields which should be filled by customer
// it would be great to provide at least an email address field
$invoice->addField($type = 'email', $mandatory = true, $defaultValue = 'my-customer@example.com');
$invoice->addField($type = 'company', $mandatory = true, $defaultValue = 'Ueli Kramer Firma');
$invoice->addField($type = 'forename', $mandatory = true, $defaultValue = 'Ueli');
$invoice->addField($type = 'surname', $mandatory = true, $defaultValue = 'Kramer');
$invoice->addField($type = 'country', $mandatory = true, $defaultValue = 'AT');
$invoice->addField($type = 'title', $mandatory = true, $defaultValue = 'miss');
$invoice->addField($type = 'terms', $mandatory = true);
$invoice->addField($type = 'custom_field_1', $mandatory = true, $defaultValue = 'Value 001', $name = 'Das ist ein Feld');

// fire request with created and filled link request-object.
try {
    $response = $alltobill->create($invoice);
    var_dump($response);
} catch (\Alltobill\AlltobillException $e) {
    print $e->getMessage();
}
