<?php
require_once "api/src/models/Cliente.php";
require_once "api/src/DAO/ClienteDAO.php";
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";

class ClienteControl
{
    public function index(): never
    {
        try {
            $clienteDAO = new ClienteDAO();
            $clientes = $clienteDAO->readAll();

            (new Response(
                success: true,
                message: 'Clientes recuperados com sucesso',
                data: ['clientes' => $clientes],
                httpCode: 200
            ))->send();
        } catch (Throwable $e) {
            Logger::Log($e);
            (new Response(
                success: false,
                message: 'Erro ao recuperar clientes',
                error: ['message' => $e->getMessage()],
                httpCode: 500
            ))->send();
        }
        exit();
    }

    public function show(int $idCliente): never
    {
        try {
            $clienteDAO = new ClienteDAO();
            $clientes = $clienteDAO->readById($idCliente);

            if (!empty($clientes)) {
                (new Response(
                    success: true,
                    message: 'Cliente encontrado com sucesso',
                    data: ['clientes' => $clientes[0]], // Pega o primeiro cliente do array
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Cliente não encontrado',
                    httpCode: 404
                ))->send();
            }
        } catch (Throwable $e) {
            Logger::Log($e);
            (new Response(
                success: false,
                message: 'Erro ao buscar cliente',
                error: ['message' => $e->getMessage()],
                httpCode: 500
            ))->send();
        }
        exit();
    }

    public function listPaginated(int $page = 1, int $limit = 10): never
    {
        try {
            if ($page < 1) $page = 1;
            if ($limit < 1) $limit = 10;

            $clienteDAO = new ClienteDAO();
            $clientes = $clienteDAO->readByPage($page, $limit);

            (new Response(
                success: true,
                message: 'Clientes recuperados com sucesso',
                data: [
                    'page' => $page,
                    'limit' => $limit,
                    'clientes' => $clientes
                ],
                httpCode: 200
            ))->send();
        } catch (Throwable $e) {
            Logger::Log($e);
            (new Response(
                success: false,
                message: 'Erro ao recuperar clientes paginados',
                error: ['message' => $e->getMessage()],
                httpCode: 500
            ))->send();
        }
        exit();
    }

    public function store(stdClass $stdCliente): never
    {
        try {
            $cliente = new Cliente();
            $cliente->setNomeCliente($stdCliente->cliente->nome_cliente)
                    ->setEmailCliente($stdCliente->cliente->email_cliente)
                    ->setIdCliente($stdCliente->cliente->idclientes);

            $clienteDAO = new ClienteDAO();
            $novoCliente = $clienteDAO->create($cliente);

            (new Response(
                success: true,
                message: 'Cliente cadastrado com sucesso',
                data: ['clientes' => $novoCliente],
                httpCode: 201
            ))->send();
        } catch (Throwable $e) {
            Logger::Log($e);
            (new Response(
                success: false,
                message: 'Erro ao cadastrar cliente',
                error: ['message' => $e->getMessage()],
                httpCode: 500
            ))->send();
        }
        exit();
    }

    public function edit(stdClass $stdCliente): never
    {
        try {
            $cliente = new Cliente();
            $cliente->setIdCliente($stdCliente->cliente->idclientes)
                   ->setNomeCliente($stdCliente->cliente->nome_cliente)
                   ->setEmailCliente($stdCliente->cliente->email_cliente);

            $clienteDAO = new ClienteDAO();

            if ($clienteDAO->update($cliente)) {
                (new Response(
                    success: true,
                    message: "Cliente atualizado com sucesso",
                    data: ['clientes' => $cliente],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: "Não foi possível atualizar o cliente",
                    error: [
                        'code' => 'update_error',
                        'message' => 'Erro ao atualizar cliente'
                    ],
                    httpCode: 400
                ))->send();
            }
        } catch (Throwable $e) {
            Logger::Log($e);
            (new Response(
                success: false,
                message: 'Erro ao atualizar cliente',
                error: ['message' => $e->getMessage()],
                httpCode: 500
            ))->send();
        }
        exit();
    }

    public function destroy(int $idCliente): never
    {
        try {
            $clienteDAO = new ClienteDAO();

            if ($clienteDAO->delete($idCliente)) {
                (new Response(httpCode: 204))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Não foi possível excluir o cliente',
                    error: [
                        'code' => 'delete_error',
                        'message' => 'O cliente não pode ser excluído'
                    ],
                    httpCode: 400
                ))->send();
            }
        } catch (Throwable $e) {
            Logger::Log($e);
            (new Response(
                success: false,
                message: 'Erro ao excluir cliente',
                error: ['message' => $e->getMessage()],
                httpCode: 500
            ))->send();
        }
        exit();
    }
    public function exportCSV(): never
    {
        $clienteDAO = new ClienteDAO();
        $clientes = $clienteDAO->readAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes.csv"');

        $saida = fopen('php://output', 'w');
        fputcsv($saida, ['ID', 'Nome', 'Email']);

        foreach ($clientes as $cliente) {
            fputcsv($saida, [
                $cliente->getIdCliente(),
                $cliente->getNomeCliente(),
                $cliente->getEmailCliente()
            ]);
        }

        fclose($saida);
        exit();
    }

    public function exportJSON(): never
    {
        $clienteDAO = new ClienteDAO();
        $clientes = $clienteDAO->readAll();

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes.json"');

        $exportar = [];
        foreach ($clientes as $cliente) {
            $exportar[] = [
                'idclientes' => $cliente->getIdCliente(),
                'nome_cliente' => $cliente->getNomeCliente(),
                'email_cliente' => $cliente->getEmailCliente()
            ];
        }

        echo json_encode(['clientes' => $exportar]);
        exit();
    }

    public function exportXML(): never
    {
        $clienteDAO = new ClienteDAO();
        $clientes = $clienteDAO->readAll();

        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="clientes.xml"');

        $xml = new SimpleXMLElement('<clientes/>');
        foreach ($clientes as $cliente) {
            $clienteXML = $xml->addChild('cliente');
            $clienteXML->addChild('idclientes', $cliente->getIdCliente());
            $clienteXML->addChild('nome_cliente', $cliente->getNomeCliente());
            $clienteXML->addChild('email_cliente', $cliente->getEmailCliente());
        }

        echo $xml->asXML();
        exit();
    }

    public function importCSV(array $csvFile): never
    {
        $nomeTemporario = $csvFile['tmp_name'];
        $ponteiroArquivo = fopen($nomeTemporario, "r");

        $clienteDAO = new ClienteDAO();
        $clientesCriados = [];
        $clientesNaoCriados = [];

        // Pular cabeçalho se existir
        fgetcsv($ponteiroArquivo);

        while (($linha = fgetcsv($ponteiroArquivo, 1000, ",")) !== false) {
            $cliente = new Cliente();
            $cliente->setNomeCliente($linha[1])
                    ->setEmailCliente($linha[2]);

            $clienteCriado = $clienteDAO->create($cliente);

            if ($clienteCriado) {
                $clientesCriados[] = $clienteCriado;
            } else {
                $clientesNaoCriados[] = $cliente;
            }
        }

        fclose($ponteiroArquivo);

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "clientesCriados" => $clientesCriados,
                "clientesNaoCriados" => $clientesNaoCriados
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

        if (!isset($dados->clientes)) {
            (new Response(
                success: false,
                message: 'Formato JSON inválido',
                httpCode: 400
            ))->send();
            exit();
        }

        $clienteDAO = new ClienteDAO();
        $clientesCriados = [];
        $clientesNaoCriados = [];

        foreach ($dados->clientes as $clienteData) {
            $cliente = new Cliente();
            $cliente->setNomeCliente($clienteData->nome_cliente)
                    ->setEmailCliente($clienteData->email_cliente);

            $clienteCriado = $clienteDAO->create($cliente);

            if ($clienteCriado) {
                $clientesCriados[] = $clienteCriado;
            } else {
                $clientesNaoCriados[] = $cliente;
            }
        }

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "clientesCriados" => $clientesCriados,
                "clientesNaoCriados" => $clientesNaoCriados
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

        $clienteDAO = new ClienteDAO();
        $clientesCriados = [];
        $clientesNaoCriados = [];

        foreach ($xml->cliente as $clienteNode) {
            $cliente = new Cliente();
            $cliente->setNomeCliente((string)$clienteNode->nome_cliente)
                    ->setEmailCliente((string)$clienteNode->email_cliente);

            $clienteCriado = $clienteDAO->create($cliente);

            if ($clienteCriado) {
                $clientesCriados[] = $clienteCriado;
            } else {
                $clientesNaoCriados[] = $cliente;
            }
        }

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "clientesCriados" => $clientesCriados,
                "clientesNaoCriados" => $clientesNaoCriados
            ],
            httpCode: 200
        ))->send();
        exit();
    }
}