<?php

namespace GiovanniALO\DataValidation;

use GiovanniALO\DataValidation\Repository\DataRepository;
use GiovanniALO\DataValidation\Router\RouterInterface;

class Validation
{
    /**
     * @var Validation
     */
    private static Validation $instance;

    /**
     * @param  RouterInterface|null  $router
     * @param  DataRepository|null  $repository
     * @param  array  $data
     * @param  array  $errors
     */
    private function __construct(
        protected ?RouterInterface $router,
        protected ?DataRepository $repository,
        protected array $data = [],
        protected array $errors = []
    ) {
    }

    /**
     * @param  RouterInterface|null  $router
     * @param  DataRepository|null  $repository
     * @return void
     */
    public static function init(
        ?RouterInterface $router = null,
        ?DataRepository $repository = null
    ): void {
        self::$instance = new Validation($router, $repository);
    }

    /**
     * @param  array  $rules
     * @param  array  $data
     * @return void
     */
    public static function validate(array $rules, array $data = []): void
    {
        self::$instance->prepareData($data);

        foreach ($rules as $field => $rule) {
            $rules = is_array($rule) ? $rule : explode('|', $rule);

            $value = self::$instance->data[$field] ?? '';

            if (!$value && !in_array('required', $rules)) {
                continue;
            }

            foreach ($rules as $filter) {
                if (strpos($filter, ':')) {
                    [$filter, $param] = explode(':', $filter);
                }

                self::$instance->$filter($field, $value, $param ?? '');

                if (!$value && $filter == 'required') {
                    break;
                }
            }
        }

        self::$instance->render();
    }

    /**
     * @param  array  $data
     * @return void
     */
    protected function prepareData(array $data): void
    {
        if ($this->router) {
            self::$instance->data = self::$instance?->router?->data() ?? [];
        } else {
            self::$instance->data = $data;
        }
    }

    /**
     * @param  string  $field
     * @param $value
     * @return void
     */
    protected function string(string $field, $value): void
    {
        if (!is_string($value)) {
            $this->errors[$field][] = 'Deve ser uma string';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function integer(string $field, string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field][] = 'Deve ser um número inteiro';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function float(string $field, string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field][] = 'Deve ser um número decimal';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function boolean(string $field, string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field][] = 'Deve ser um valor booleano';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function date(string $field, string $value): void
    {
        if (!strtotime($value)) {
            $this->errors[$field][] = 'Deve ser uma data válida';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function url(string $field, string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field][] = 'Deve ser uma URL válida';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function required(string $field, string $value): void
    {
        if (!$value) {
            $this->errors[$field][] = 'É obrigatório';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function email(string $field, string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Deve ser um endereço de e-mail válido';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @param  int  $length
     * @return void
     */
    protected function min(string $field, string $value, int $length): void
    {
        if (strlen($value) < $length) {
            $this->errors[$field][] = 'Deve conter pelo menos '.$length.' caracteres';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @param  int  $length
     * @return void
     */
    protected function max(string $field, string $value, int $length): void
    {
        if (strlen($value) > $length) {
            $this->errors[$field][] = 'Deve conter no máximo '.$length.' caracteres';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function password(string $field, string $value): void
    {
        if (!preg_match('/^(?=.[a-z])(?=.[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $value)) {
            $this->errors[$field][] = 'Deve conter pelo menos 8 caracteres, uma'.
                ' letra maiúscula, uma letra minúscula e um número';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @return void
     */
    protected function confirmation(string $field, string $value): void
    {
        if (!isset($this->data[$field.'_confirmation'])) {
            $this->errors[$field][] = 'A confirmação é obrigatória';
        } elseif ($value != $this->data[$field.'_confirmation']) {
            $this->errors[$field][] = 'A confirmação não confere';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @param  string  $str
     * @return void
     */
    protected function unique(string $field, string $value, string $str): void
    {
        if ($this->findInDatabase($value, $str)) {
            $this->errors[$field][] = 'Já está em uso';
        }
    }

    /**
     * @param  string  $field
     * @param  string  $value
     * @param  string  $str
     * @return void
     */
    protected function exists(string $field, string $value, string $str): void
    {
        if (!$this->findInDatabase($value, $str)) {
            $this->errors[$field][] = 'Não existe no banco de dados';
        }
    }

    /**
     * @param  string  $value
     * @param  string  $str
     * @return object|null
     */
    private function findInDatabase(string $value, string $str): ?object
    {
        [$namespace, $column] = explode(',', $str);

        return $this?->repository?->find(
            $namespace,
            "{$column} = :{$column}",
            [$column => $value]
        );
    }

    /**
     * @return void
     */
    protected function render(): void
    {
        if (count($this->errors)) {
            http_response_code(400);

            echo json_encode(['errors' => $this->errors]);

            exit;
        }
    }
}
