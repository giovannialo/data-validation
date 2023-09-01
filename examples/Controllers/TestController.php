<?php

namespace Example\Controllers;

use Example\Models\User;
use GiovanniALO\DataValidation\Validation;

class TestController
{
    public function withCoffeeCodeRouter(): void
    {
        Validation::validate([
            'first_name' => ['required', 'string', 'min:2', 'max:20'],
            'last_name' => ['required', 'string', 'min:2', 'max:40'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:40', 'confirmation'],
        ]);

        echo 'Ok';
    }

    public function withCoffeeCodeDataLayer(array $data): void
    {
        Validation::validate([
            'first_name' => ['required', 'string', 'min:2', 'max:20'],
            'last_name' => ['required', 'string', 'min:2', 'max:40'],
            'email' => ['required', 'string', 'email', 'unique:'.User::class.',email'],
            'password' => ['required', 'string', 'min:8', 'max:40', 'confirmation'],
        ]);

        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]);
        $saved = $user->save();

        if (!$saved) {
            http_response_code(500);

            echo json_encode([
                'message' => 'Erro ao cadastrar usuário.',
                'trace' => $user->fail()->getMessage(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            exit;
        }

        echo json_encode([
            'message' => 'Usuário cadastrado com sucesso.',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
