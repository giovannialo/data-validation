<?php

use CoffeeCode\Router\Router;
use GiovanniALO\DataValidation\Repository\CoffeeCodeDataRepository;
use GiovanniALO\DataValidation\Router\CoffeeCodeRouter;
use GiovanniALO\DataValidation\Validation;

require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/config-db.php';

require __DIR__.'/Controllers/TestController.php';

require __DIR__.'/Models/User.php';


$router = new Router('http://localhost/data-validation');

$forwarder = new CoffeeCodeRouter($router);

$repository = new CoffeeCodeDataRepository();

Validation::init($forwarder, $repository);

$router->namespace('Controllers');

$router->post('/test', 'TestController:withCoffeeCodeDataLayer');

$router->dispatch();

if ($router->error()) {
    echo $router->error();

    die;
}
