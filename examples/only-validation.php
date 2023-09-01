<?php

require __DIR__.'/../vendor/autoload.php';

use GiovanniALO\DataValidation\Validation;

$data = [
    'first_name' => 'Giovanni',
    'last_name' => 'Oliveira',
    'email' => 'giovanni.al.oliveira@gmail.com',
    'password' => 'giovanni',
    'password_confirmation' => 'giovanni',
];

Validation::init();

Validation::validate([
    'first_name' => ['required', 'min:2', 'max:20'],
    'last_name' => ['required', 'min:2', 'max:40'],
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8', 'max:40', 'confirmation'],
], $data);

echo 'Ok';
