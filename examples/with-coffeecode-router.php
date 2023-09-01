<?php

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/Controllers/TestController.php';

use CoffeeCode\Router\Router;
use GiovanniALO\DataValidation\Router\CoffeeCodeRouter;
use GiovanniALO\DataValidation\Validation;

$router = new Router('http://localhost/data-validation');

$forwarder = new CoffeeCodeRouter($router);

Validation::init($forwarder);

$router->namespace('Controllers');

$router->post('/test', 'TestController:withCoffeeCodeRouter');

$router->dispatch();

if ($router->error()) {
    echo $router->error();

    exit;
}
