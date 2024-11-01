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

$subscription = new \Alltobill\Models\Request\Subscription();
$subscription->setId(6);
$subscription->setAmount(40000);
$subscription->setCurrency('CHF');
try {
    $response = $alltobill->update($subscription);
    var_dump($response);
} catch (\Alltobill\AlltobillException $e) {
    print $e->getMessage();
}
