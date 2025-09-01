<?php
require_once "api/src/http/Response.php";

class ProdutoMiddleware
{
    /**
     * Converte uma string JSON em um objeto stdClass para Produto
     */
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdProduto = json_decode($requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'JSON inválido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdProduto->produto)) {
            (new Response(
                success: false,
                message: 'Produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o objeto Produto',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdProduto->produto->nome_produto)) {
            (new Response(
                success: false,
                message: 'Produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo nome_produto',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdProduto->produto->preco_produto)) {
            (new Response(
                success: false,
                message: 'Produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo preco_produto',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $stdProduto;
    }

    /**
     * Valida o nome do produto
     */
    public function isValidNomeProduto($nomeProduto): self
    {
        if (!isset($nomeProduto)) {
            (new Response(
                success: false,
                message: 'Nome do produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O nome não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (strlen($nomeProduto) < 3) {
            (new Response(
                success: false,
                message: 'Nome do produto inválido',
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
     * Valida o preço do produto
     */
    public function isValidPreco($preco): self
    {
        if (!isset($preco)) {
            (new Response(
                success: false,
                message: 'Preço inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O preço não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric($preco)) {
            (new Response(
                success: false,
                message: 'Preço inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O preço deve ser numérico'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($preco <= 0) {
            (new Response(
                success: false,
                message: 'Preço inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O preço deve ser maior que zero'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Verifica se já existe um produto com o nome fornecido
     */
    public function hasNotProdutoByName($nomeProduto): self
    {
        require_once "api/src/DAO/ProdutoDAO.php";
        $produtoDAO = new ProdutoDAO();
        $produto = $produtoDAO->readByName($nomeProduto);

        if (isset($produto)) {
            (new Response(
                success: false,
                message: 'Produto já cadastrado',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Já existe um produto com este nome'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $this;
    }

    /**
     * Valida o ID do produto
     */
    public function isValidId($idProduto): self
    {
        if (!isset($idProduto)) {
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
        } else if (!is_numeric($idProduto)) {
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
        } else if ($idProduto <= 0) {
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
     * Verifica se existe um produto com o ID fornecido
     */
    public function hasProdutoById($idProduto): self
    {
        require_once "api/src/DAO/ProdutoDAO.php";
        $produtoDAO = new ProdutoDAO();
        $produto = $produtoDAO->readById($idProduto);

        if (!isset($produto)) {
            (new Response(
                success: false,
                message: 'Produto não encontrado',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não existe produto com o ID fornecido'
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