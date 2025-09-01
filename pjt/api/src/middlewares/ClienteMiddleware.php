<?php
require_once "api/src/http/Response.php";

class ClienteMiddleware
{
    /**
     * Converte uma string JSON em um objeto stdClass para Cliente
     */
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdCliente = json_decode($requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'JSON inválido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdCliente->cliente)) {
            (new Response(
                success: false,
                message: 'Cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o objeto Cliente',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdCliente->cliente->nome_cliente)) {
            (new Response(
                success: false,
                message: 'Cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo nome_cliente',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdCliente->cliente->email_cliente)) {
            (new Response(
                success: false,
                message: 'Cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo email_cliente',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $stdCliente;
    }

    /**
     * Valida o nome do cliente
     */
    public function isValidNomeCliente($nomeCliente): self
    {
        if (!isset($nomeCliente)) {
            (new Response(
                success: false,
                message: 'Nome do cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O nome não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (strlen($nomeCliente) < 3) {
            (new Response(
                success: false,
                message: 'Nome do cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O nome precisa ter pelo menos 3 caracteres'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Valida o email do cliente
     */
    public function isValidEmail($email): self
    {
        if (!isset($email)) {
            (new Response(
                success: false,
                message: 'Email inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O email não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            (new Response(
                success: false,
                message: 'Email inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Formato de email inválido'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Verifica se já existe um cliente com o email fornecido
     */
    public function hasNotClienteByEmail($email): self
    {
        require_once "api/src/DAO/ClienteDAO.php";
        $clienteDAO = new ClienteDAO();
        $cliente = $clienteDAO->readByEmail($email);

        if (isset($cliente)) {
            (new Response(
                success: false,
                message: 'Email já cadastrado',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Já existe um cliente com este email'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Valida o ID do cliente
     */
    public function isValidId($idCliente): self
    {
        if (!isset($idCliente)) {
            (new Response(
                success: false,
                message: 'ID inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric($idCliente)) {
            (new Response(
                success: false,
                message: 'ID inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID deve ser numérico'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($idCliente <= 0) {
            (new Response(
                success: false,
                message: 'ID inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID deve ser maior que zero'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Verifica se existe um cliente com o ID fornecido
     */
    public function hasClienteById($idCliente): self
    {
        require_once "api/src/DAO/ClienteDAO.php";
        $clienteDAO = new ClienteDAO();
        $cliente = $clienteDAO->readById($idCliente);

        if (!isset($cliente)) {
            (new Response(
                success: false,
                message: 'Cliente não encontrado',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não existe cliente com o ID fornecido'
                ],
                httpCode: 404
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Valida a página para paginação
     */
    public function isValidPage($page): self
    {
        if (!is_numeric($page)) {
            (new Response(
                success: false,
                message: 'Página inválida',
                error: [
                    'code' => 'validation_error',
                    'message' => 'A página deve ser um número'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($page <= 0) {
            (new Response(
                success: false,
                message: 'Página inválida',
                error: [
                    'code' => 'validation_error',
                    'message' => 'A página deve ser maior que zero'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Valida o limite para paginação
     */
    public function isValidLimit($limit): self
    {
        if (!is_numeric($limit)) {
            (new Response(
                success: false,
                message: 'Limite inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O limite deve ser um número'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($limit <= 0) {
            (new Response(
                success: false,
                message: 'Limite inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O limite deve ser maior que zero'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }
}