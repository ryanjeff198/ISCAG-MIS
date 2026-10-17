<?php
/**
 * PayMongo API Configuration Template
 * 
 * Instructions:
 * 1. Copy this file to paymongo.php
 * 2. Update the credentials with your actual PayMongo test or live API keys.
 */
return [
    'secret_key'  => 'YOUR_PAYMONGO_SECRET_KEY',
    'public_key'  => 'YOUR_PAYMONGO_PUBLIC_KEY',
    'api_base'    => 'https://api.paymongo.com/v1',
    'currency'    => 'PHP',
    'payment_method_types' => [
        'gcash',
        'paymaya',
        'card',
        'dob', // Direct Online Banking
        'grab_pay'
    ]
];
