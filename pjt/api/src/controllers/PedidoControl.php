<?php
require_once "api/src/models/Pedido.php";
require_once "api/src/models/Cliente.php";
require_once "api/src/models/Produto.php";
require_once "api/src/DAO/PedidoDAO.php";
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";

class PedidoControl
{
    public function index(): never
    {
        $pedidoDAO = new PedidoDAO();
        $pedidos = $pedidoDAO->readAll();

        (new Response(
            success: true,
            message: 'Pedidos recuperados com sucesso',
            data: ['pedidos' => $pedidos],
            httpCode: 200
        ))->send();
        exit();
    }

    public function show(int $idPedido): never
    {
        $pedidoDAO = new PedidoDAO();
        $pedido = $pedidoDAO->readById($idPedido);

        if (isset($pedido)) {
            (new Response(
                success: true,
                message: 'Pedido encontrado com sucesso',
                data: ['pedidos' => $pedido],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: 'Pedido não encontrado',
                httpCode: 404
            ))->send();
        }
        exit();
    }

    public function listPaginated(int $page = 1, int $limit = 10): never
    {
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $pedidoDAO = new PedidoDAO();
        $pedidos = $pedidoDAO->readByPage($page, $limit);

        (new Response(
            success: true,
            message: 'Pedidos recuperados com sucesso',
            data: [
                'page' => $page,
                'limit' => $limit,
                'pedidos' => $pedidos
            ],
            httpCode: 200
        ))->send();
        exit();
    }

    public function store(stdClass $stdPedido): never
    {
        $pedido = new Pedido();
        $pedido->setDataPedido($stdPedido->pedido->data_pedido)
        ->setIdPedido($stdPedido->pedido->idpedidos);
        
        $cliente = new Cliente();
        $cliente->setIdCliente($stdPedido->pedido->clientes_idclientes);
        $pedido->setCliente($cliente);
        
        $produto = new Produto();
        $produto->setIdProduto($stdPedido->pedido->produtos_idprodutos);
        $pedido->setProduto($produto);

        $pedidoDAO = new PedidoDAO();
        $novoPedido = $pedidoDAO->create($pedido);

        (new Response(
            success: true,
            message: 'Pedido cadastrado com sucesso',
            data: ['pedidos' => $novoPedido],
            httpCode: 201
        ))->send();
        exit();
    }

    public function edit(stdClass $stdPedido): never
    {
        $pedido = new Pedido();
        $pedido->setIdPedido($stdPedido->pedido->idpedidos)
               ->setDataPedido($stdPedido->pedido->data_pedido);
        
        $cliente = new Cliente();
        $cliente->setIdCliente($stdPedido->pedido->clientes_idclientes);
        $pedido->setCliente($cliente);
        
        $produto = new Produto();
        $produto->setIdProduto($stdPedido->pedido->produtos_idprodutos);
        $pedido->setProduto($produto);

        $pedidoDAO = new PedidoDAO();

        if ($pedidoDAO->update($pedido)) {
            (new Response(
                success: true,
                message: "Pedido atualizado com sucesso",
                data: ['pedidos' => $pedido],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: "Não foi possível atualizar o pedido",
                error: [
                    'code' => 'update_error',
                    'message' => 'Erro ao atualizar pedido'
                ],
                httpCode: 400
            ))->send();
        }
        exit();
    }

    public function destroy(int $idPedido): never
    {
        $pedidoDAO = new PedidoDAO();

        if ($pedidoDAO->delete($idPedido)) {
            (new Response(httpCode: 204))->send();
        } else {
            (new Response(
                success: false,
                message: 'Não foi possível excluir o pedido',
                error: [
                    'code' => 'delete_error',
                    'message' => 'O pedido não pode ser excluído'
                ],
                httpCode: 400
            ))->send();
        }
        exit();
    }

    public function exportCSV(): never
    {
        $pedidoDAO = new PedidoDAO();
        $pedidos = $pedidoDAO->readAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="pedidos.csv"');

        $saida = fopen('php://output', 'w');
        fputcsv($saida, ['ID', 'Data', 'ID Cliente', 'ID Produto']);

        foreach ($pedidos as $pedido) {
            fputcsv($saida, [
                $pedido->getIdPedido(),
                $pedido->getDataPedido(),
                $pedido->getCliente()->getIdCliente(),
                $pedido->getProduto()->getIdProduto()
            ]);
        }

        fclose($saida);
        exit();
    }

    public function exportJSON(): never
    {
        $pedidoDAO = new PedidoDAO();
        $pedidos = $pedidoDAO->readAll();

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="pedidos.json"');

        $exportar = [];
        foreach ($pedidos as $pedido) {
            $exportar[] = [
                'idpedidos' => $pedido->getIdPedido(),
                'data_pedido' => $pedido->getDataPedido(),
                'clientes_idclientes' => $pedido->getCliente()->getIdCliente(),
                'produtos_idprodutos' => $pedido->getProduto()->getIdProduto()
            ];
        }

        echo json_encode(['pedidos' => $exportar]);
        exit();
    }

    public function exportXML(): never
    {
        $pedidoDAO = new PedidoDAO();
        $pedidos = $pedidoDAO->readAll();

        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="pedidos.xml"');

        $xml = new SimpleXMLElement('<pedidos/>');
        foreach ($pedidos as $pedido) {
            $pedidoXML = $xml->addChild('pedido');
            $pedidoXML->addChild('idpedidos', $pedido->getIdPedido());
            $pedidoXML->addChild('data_pedido', $pedido->getDataPedido());
            $pedidoXML->addChild('clientes_idclientes', $pedido->getCliente()->getIdCliente());
            $pedidoXML->addChild('produtos_idprodutos', $pedido->getProduto()->getIdProduto());
        }

        echo $xml->asXML();
        exit();
    }

    public function importCSV(array $csvFile): never
    {
        $nomeTemporario = $csvFile['tmp_name'];
        $ponteiroArquivo = fopen($nomeTemporario, "r");

        $pedidoDAO = new PedidoDAO();
        $pedidosCriados = [];
        $pedidosNaoCriados = [];

        // Pular cabeçalho se existir
        fgetcsv($ponteiroArquivo);

        while (($linha = fgetcsv($ponteiroArquivo, 1000, ",")) !== false) {
            $pedido = new Pedido();
            $pedido->setDataPedido($linha[1]);
            
            $cliente = new Cliente();
            $cliente->setIdCliente($linha[2]);
            $pedido->setCliente($cliente);
            
            $produto = new Produto();
            $produto->setIdProduto($linha[3]);
            $pedido->setProduto($produto);

            $pedidoCriado = $pedidoDAO->create($pedido);

            if ($pedidoCriado) {
                $pedidosCriados[] = $pedidoCriado;
            } else {
                $pedidosNaoCriados[] = $pedido;
            }
        }

        fclose($ponteiroArquivo);

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "pedidosCriados" => $pedidosCriados,
                "pedidosNaoCriados" => $pedidosNaoCriados
            ],
            httpCode: 200
        ))->send();
        exit();
    }

    public function importJSON(array $jsonFile): never
    {
        $nomeTemporario = $jsonFile['tmp_name'];
        $conteudo = file_get_contents($nomeTemporario);
        $dados = json_decode($conteudo);

        if (!isset($dados->pedidos)) {
            (new Response(
                success: false,
                message: 'Formato JSON inválido',
                httpCode: 400
            ))->send();
            exit();
        }

        $pedidoDAO = new PedidoDAO();
        $pedidosCriados = [];
        $pedidosNaoCriados = [];

        foreach ($dados->pedidos as $pedidoData) {
            $pedido = new Pedido();
            $pedido->setDataPedido($pedidoData->data_pedido);
            
            $cliente = new Cliente();
            $cliente->setIdCliente($pedidoData->clientes_idclientes);
            $pedido->setCliente($cliente);
            
            $produto = new Produto();
            $produto->setIdProduto($pedidoData->produtos_idprodutos);
            $pedido->setProduto($produto);

            $pedidoCriado = $pedidoDAO->create($pedido);

            if ($pedidoCriado) {
                $pedidosCriados[] = $pedidoCriado;
            } else {
                $pedidosNaoCriados[] = $pedido;
            }
        }

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "pedidosCriados" => $pedidosCriados,
                "pedidosNaoCriados" => $pedidosNaoCriados
            ],
            httpCode: 200
        ))->send();
        exit();
    }

    public function importXML(array $xmlFile): never
    {
        $nomeTemporario = $xmlFile['tmp_name'];
        $xml = simplexml_load_file($nomeTemporario);

        if (!$xml) {
            (new Response(
                success: false,
                message: 'Erro ao carregar arquivo XML',
                httpCode: 400
            ))->send();
            exit();
        }

        $pedidoDAO = new PedidoDAO();
        $pedidosCriados = [];
        $pedidosNaoCriados = [];

        foreach ($xml->pedido as $pedidoNode) {
            $pedido = new Pedido();
            $pedido->setDataPedido((string)$pedidoNode->data_pedido);
            
            $cliente = new Cliente();
            $cliente->setIdCliente((int)$pedidoNode->clientes_idclientes);
            $pedido->setCliente($cliente);
            
            $produto = new Produto();
            $produto->setIdProduto((int)$pedidoNode->produtos_idprodutos);
            $pedido->setProduto($produto);

            $pedidoCriado = $pedidoDAO->create($pedido);

            if ($pedidoCriado) {
                $pedidosCriados[] = $pedidoCriado;
            } else {
                $pedidosNaoCriados[] = $pedido;
            }
        }

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "pedidosCriados" => $pedidosCriados,
                "pedidosNaoCriados" => $pedidosNaoCriados
            ],
            httpCode: 200
        ))->send();
        exit();
    }
}