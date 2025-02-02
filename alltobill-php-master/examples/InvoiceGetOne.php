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

$invoice = new \Alltobill\Models\Request\Invoice();
$invoice->setId(2);
try {
    $response = $alltobill->getOne($invoice);
    var_dump($response);
} catch (\Alltobill\AlltobillException $e) {
    print $e->getMessage();
}
