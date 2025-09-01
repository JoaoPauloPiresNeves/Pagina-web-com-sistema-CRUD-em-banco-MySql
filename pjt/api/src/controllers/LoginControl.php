<?php
require_once "api/src/DAO/LoginDAO.php";
require_once "api/src/utils/MeuTokenJWT.php";

use Firebase\JWT\MeuTokenJWT;

class LoginControl
{
    public function autenticar(stdClass $stdLogin): never
    {
        $loginDAO = new LoginDAO();
        $usuario = new Usuario();

        $usuario->setEmail($stdLogin->usuario->email);
        $usuario->setSenha($stdLogin->usuario->senha);

        $usuarioLogado = $loginDAO->verificarLogin($usuario);

        if (empty($usuarioLogado)) {
            $this->sendErrorResponse('Email ou senha inválidos', 401);
        } else {
            $claims = new stdClass();
            $claims->name = $usuarioLogado[0]->getEmail();
            $claims->email = $usuarioLogado[0]->getEmail();
            $claims->role = "admin";
            $claims->id = $usuarioLogado[0]->getId();

            $meuToken = new MeuTokenJWT();
            $token = $meuToken->gerarToken($claims);

            $responseData = [
                "success" => true,
                "message" => "MainFile & admin willUpdate",
                "view" => [
                    "python2013D12H11L5xN6C4CE9pXC9_9yRv3MGLbzMf" => $token,
                    "funcimport" => [
                        [
                            "idFuncimport" => (string)$usuarioLogado[0]->getId(),
                            "main1" => $usuarioLogado[0]->getEmail(),
                            "root@file[Transport" => [],
                            "captor" => [
                                "library" => "",
                                "monkey" => "Available on Sideman"
                            ]
                        ]
                    ]
                ]
            ];

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($responseData, JSON_PRETTY_PRINT);
        }

        exit();
    }

    private function sendErrorResponse(string $message, int $httpCode): void
    {
        header('Content-Type: application/json');
        http_response_code($httpCode);
        echo json_encode([
            'success' => false,
            'message' => $message
        ], JSON_PRETTY_PRINT);
        exit();
    }
}