<?php

namespace GiovanniALO\DataValidation\Router;

use CoffeeCode\Router\Router;

class CoffeeCodeRouter implements RouterInterface
{
    /**
     * @param  Router  $router
     */
    public function __construct(protected Router $router)
    {
    }

    /**
     * @return array|null
     */
    public function data(): ?array
    {
        return $this->router->data();
    }
}
