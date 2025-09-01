<?php
require_once "api/src/http/Response.php";

class PedidoMiddleware
{
    /**
     * Converte uma string JSON em um objeto stdClass para Pedido
     */
    public function stringJsonToStdClass($requestBody): stdClass
    {
        $stdPedido = json_decode($requestBody);

        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Pedido inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'JSON inválido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdPedido->pedido)) {
            (new Response(
                success: false,
                message: 'Pedido inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o objeto Pedido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdPedido->pedido->data_pedido)) {
            (new Response(
                success: false,
                message: 'Pedido inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo data_pedido',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdPedido->pedido->clientes_idclientes)) {
            (new Response(
                success: false,
                message: 'Pedido inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo clientes_idclientes',
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!isset($stdPedido->pedido->produtos_idprodutos)) {
            (new Response(
                success: false,
                message: 'Pedido inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não foi enviado o atributo produtos_idprodutos',
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        return $stdPedido;
    }

    /**
     * Valida a data do pedido
     */
    public function isValidDataPedido($dataPedido): self
    {
        if (!isset($dataPedido)) {
            (new Response(
                success: false,
                message: 'Data inválida',
                error: [
                    'code' => 'validation_error',
                    'message' => 'A data não foi enviada'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        $data = DateTime::createFromFormat('Y-m-d', $dataPedido);
        if (!$data || $data->format('Y-m-d') !== $dataPedido) {
            (new Response(
                success: false,
                message: 'Data inválida',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Formato de data inválido (use YYYY-MM-DD)'
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
    public function isValidClienteId($clienteId): self
    {
        if (!isset($clienteId)) {
            (new Response(
                success: false,
                message: 'ID do cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID do cliente não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric($clienteId)) {
            (new Response(
                success: false,
                message: 'ID do cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID do cliente deve ser numérico'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($clienteId <= 0) {
            (new Response(
                success: false,
                message: 'ID do cliente inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID do cliente deve ser maior que zero'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        // Verifica se o cliente existe
        require_once "api/src/DAO/ClienteDAO.php";
        $clienteDAO = new ClienteDAO();
        $cliente = $clienteDAO->readById($clienteId);

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
     * Valida o ID do produto
     */
    public function isValidProdutoId($produtoId): self
    {
        if (!isset($produtoId)) {
            (new Response(
                success: false,
                message: 'ID do produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID do produto não foi enviado'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if (!is_numeric($produtoId)) {
            (new Response(
                success: false,
                message: 'ID do produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID do produto deve ser numérico'
                ],
                httpCode: 400
            ))->send();
            exit();
        } else if ($produtoId <= 0) {
            (new Response(
                success: false,
                message: 'ID do produto inválido',
                error: [
                    'code' => 'validation_error',
                    'message' => 'O ID do produto deve ser maior que zero'
                ],
                httpCode: 400
            ))->send();
            exit();
        }

        // Verifica se o produto existe
        require_once "api/src/DAO/ProdutoDAO.php";
        $produtoDAO = new ProdutoDAO();
        $produto = $produtoDAO->readById($produtoId);

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
     * Valida o ID do pedido
     */
    public function isValidId($idPedido): self
    {
        if (!isset($idPedido)) {
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
        } else if (!is_numeric($idPedido)) {
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
        } else if ($idPedido <= 0) {
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
     * Verifica se existe um pedido com o ID fornecido
     */
    public function hasPedidoById($idPedido): self
    {
        require_once "api/src/DAO/PedidoDAO.php";
        $pedidoDAO = new PedidoDAO();
        $pedido = $pedidoDAO->readById($idPedido);

        if (!isset($pedido)) {
            (new Response(
                success: false,
                message: 'Pedido não encontrado',
                error: [
                    'code' => 'validation_error',
                    'message' => 'Não existe pedido com o ID fornecido'
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