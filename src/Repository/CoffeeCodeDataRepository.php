<?php

namespace GiovanniALO\DataValidation\Repository;

use CoffeeCode\DataLayer\DataLayer;

class CoffeeCodeDataRepository implements DataRepository
{
    /**
     * @param  string  $namespace
     * @param  string  $terms
     * @param  array|string|null  $params
     * @return mixed
     * @throws \Exception
     */
    public function find(
        string $namespace,
        string $terms,
        array|string|null $params = null
    ): mixed {
        if (!class_exists($namespace)) {
            throw new \Exception("{$namespace} não é um modelo DataLayer válido");
        }

        if ($params && is_array($params)) {
            $params = http_build_query($params);
        }

        /** @var DataLayer $model */
        $model = new $namespace();

        return $model->find($terms, $params)->fetch();
    }
}
