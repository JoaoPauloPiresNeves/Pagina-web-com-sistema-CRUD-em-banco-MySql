<?php
require_once "api/src/http/Response.php";

class LoginMiddleware
{
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdLogin = json_decode($requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendErrorResponse('JSON inválido', 'Json inválido', 400);
        }

        if (!isset($stdLogin->usuario)) {
            $this->sendErrorResponse('Login inválido', 'Não foi enviado o objeto usuario', 400);
        }

        if (!isset($stdLogin->usuario->email)) {
            $this->sendErrorResponse('Login inválido', 'Email não enviado', 400);
        }

        if (!isset($stdLogin->usuario->senha)) {
            $this->sendErrorResponse('Login inválido', 'Senha não enviada', 400);
        }

        return $stdLogin;
    }

    public function isValidEmail($email): self
    {
        if (!isset($email)) {
            $this->sendErrorResponse('Email inválido', 'Email não foi enviado', 400);
        }

        if (strlen($email) < 5) {
            $this->sendErrorResponse('Email inválido', 'Email precisa ter pelo menos 5 caracteres', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendErrorResponse('Email inválido', 'Formato de email inválido', 400);
        }

        return $this;
    }

    public function isvalidSenha(string $senha): self
    {
        if (!isset($senha)) {
            $this->sendErrorResponse('Senha inválida', 'Senha não pode estar vazia', 400);
        }

        if (strlen($senha) < 6) {
            $this->sendErrorResponse('Senha inválida', 'Senha deve ter no mínimo 6 caracteres', 400);
        }

        return $this;
    }

    private function sendErrorResponse(string $message, string $errorMessage, int $httpCode): void
    {
        header('Content-Type: application/json');
        http_response_code($httpCode);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'error' => ['message' => $errorMessage]
        ], JSON_PRETTY_PRINT);
        exit();
    }
}