<?php

namespace GiovanniALO\DataValidation\Repository;

interface DataRepository
{
    public function find(
        string $namespace,
        string $terms,
        array|string|null $params = null
    );
}
