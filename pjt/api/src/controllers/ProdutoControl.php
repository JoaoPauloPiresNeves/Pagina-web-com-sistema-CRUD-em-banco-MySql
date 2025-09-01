<?php
require_once "api/src/models/Produto.php";
require_once "api/src/DAO/ProdutoDAO.php";
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";

class ProdutoControl
{
    public function index(): never
    {
        $produtoDAO = new ProdutoDAO();
        $produtos = $produtoDAO->readAll();

        (new Response(
            success: true,
            message: 'Produtos recuperados com sucesso',
            data: ['produtos' => $produtos],
            httpCode: 200
        ))->send();
        exit();
    }

    public function show(int $idProduto): never
    {
        $produtoDAO = new ProdutoDAO();
        $produto = $produtoDAO->readById($idProduto);

        if (isset($produto)) {
            (new Response(
                success: true,
                message: 'Produto encontrado com sucesso',
                data: ['produtos' => $produto],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: 'Produto não encontrado',
                httpCode: 404
            ))->send();
        }
        exit();
    }

    public function listPaginated(int $page = 1, int $limit = 10): never
    {
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $produtoDAO = new ProdutoDAO();
        $produtos = $produtoDAO->readByPage($page, $limit);

        (new Response(
            success: true,
            message: 'Produtos recuperados com sucesso',
            data: [
                'page' => $page,
                'limit' => $limit,
                'produtos' => $produtos
            ],
            httpCode: 200
        ))->send();
        exit();
    }

    public function store(stdClass $stdProduto): never
    {
        $produto = new Produto();

        $produto->setIdProduto($stdProduto->produto->idprodutos)
        ->setNomeProduto($stdProduto->produto->nome_produto)
                ->setPrecoProduto($stdProduto->produto->preco_produto);

        $produtoDAO = new ProdutoDAO();
        $novoProduto = $produtoDAO->create($produto);

        (new Response(
            success: true,
            message: 'Produto cadastrado com sucesso',
            data: ['produtos' => $novoProduto],
            httpCode: 201
        ))->send();
        exit();
    }

    public function edit(stdClass $stdProduto): never
    {
        $produto = new Produto();
        $produto->setIdProduto($stdProduto->produto->idprodutos)
                ->setNomeProduto($stdProduto->produto->nome_produto)
                ->setPrecoProduto($stdProduto->produto->preco_produto);

        $produtoDAO = new ProdutoDAO();

        if ($produtoDAO->update($produto)) {
            (new Response(
                success: true,
                message: "Produto atualizado com sucesso",
                data: ['produtos' => $produto],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: "Não foi possível atualizar o produto",
                error: [
                    'code' => 'update_error',
                    'message' => 'Erro ao atualizar produto'
                ],
                httpCode: 400
            ))->send();
        }
        exit();
    }

    public function destroy(int $idProduto): never
    {
        $produtoDAO = new ProdutoDAO();

        if ($produtoDAO->delete($idProduto)) {
            (new Response(httpCode: 204))->send();
        } else {
            (new Response(
                success: false,
                message: 'Não foi possível excluir o produto',
                error: [
                    'code' => 'delete_error',
                    'message' => 'O produto não pode ser excluído'
                ],
                httpCode: 400
            ))->send();
        }
        exit();
    }

    public function exportCSV(): never
    {
        $produtoDAO = new ProdutoDAO();
        $produtos = $produtoDAO->readAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="produtos.csv"');

        $saida = fopen('php://output', 'w');
        fputcsv($saida, ['ID', 'Nome', 'Preço']);

        foreach ($produtos as $produto) {
            fputcsv($saida, [
                $produto->getIdProduto(),
                $produto->getNomeProduto(),
                $produto->getPrecoProduto()
            ]);
        }

        fclose($saida);
        exit();
    }

    public function exportJSON(): never
    {
        $produtoDAO = new ProdutoDAO();
        $produtos = $produtoDAO->readAll();

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="produtos.json"');

        $exportar = [];
        foreach ($produtos as $produto) {
            $exportar[] = [
                'idprodutos' => $produto->getIdProduto(),
                'nome_produto' => $produto->getNomeProduto(),
                'preco_produto' => $produto->getPrecoProduto()
            ];
        }

        echo json_encode(['produtos' => $exportar]);
        exit();
    }

    public function exportXML(): never
    {
        $produtoDAO = new ProdutoDAO();
        $produtos = $produtoDAO->readAll();

        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="produtos.xml"');

        $xml = new SimpleXMLElement('<produtos/>');
        foreach ($produtos as $produto) {
            $produtoXML = $xml->addChild('produto');
            $produtoXML->addChild('idprodutos', $produto->getIdProduto());
            $produtoXML->addChild('nome_produto', $produto->getNomeProduto());
            $produtoXML->addChild('preco_produto', $produto->getPrecoProduto());
        }

        echo $xml->asXML();
        exit();
    }

    public function importCSV(array $csvFile): never
    {
        $nomeTemporario = $csvFile['tmp_name'];
        $ponteiroArquivo = fopen($nomeTemporario, "r");

        $produtoDAO = new ProdutoDAO();
        $produtosCriados = [];
        $produtosNaoCriados = [];

        // Pular cabeçalho se existir
        fgetcsv($ponteiroArquivo);

        while (($linha = fgetcsv($ponteiroArquivo, 1000, ",")) !== false) {
            $produto = new Produto();
            $produto->setNomeProduto($linha[1])
                    ->setPrecoProduto($linha[2]);

            $produtoCriado = $produtoDAO->create($produto);

            if ($produtoCriado) {
                $produtosCriados[] = $produtoCriado;
            } else {
                $produtosNaoCriados[] = $produto;
            }
        }

        fclose($ponteiroArquivo);

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "produtosCriados" => $produtosCriados,
                "produtosNaoCriados" => $produtosNaoCriados
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

        if (!isset($dados->produtos)) {
            (new Response(
                success: false,
                message: 'Formato JSON inválido',
                httpCode: 400
            ))->send();
            exit();
        }

        $produtoDAO = new ProdutoDAO();
        $produtosCriados = [];
        $produtosNaoCriados = [];

        foreach ($dados->produtos as $produtoData) {
            $produto = new Produto();
            $produto->setNomeProduto($produtoData->nome_produto)
                    ->setPrecoProduto($produtoData->preco_produto);

            $produtoCriado = $produtoDAO->create($produto);

            if ($produtoCriado) {
                $produtosCriados[] = $produtoCriado;
            } else {
                $produtosNaoCriados[] = $produto;
            }
        }

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "produtosCriados" => $produtosCriados,
                "produtosNaoCriados" => $produtosNaoCriados
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

        $produtoDAO = new ProdutoDAO();
        $produtosCriados = [];
        $produtosNaoCriados = [];

        foreach ($xml->produto as $produtoNode) {
            $produto = new Produto();
            $produto->setNomeProduto((string)$produtoNode->nome_produto)
                    ->setPrecoProduto((float)$produtoNode->preco_produto);

            $produtoCriado = $produtoDAO->create($produto);

            if ($produtoCriado) {
                $produtosCriados[] = $produtoCriado;
            } else {
                $produtosNaoCriados[] = $produto;
            }
        }

        (new Response(
            success: true,
            message: 'Importação realizada com sucesso',
            data: [
                "produtosCriados" => $produtosCriados,
                "produtosNaoCriados" => $produtosNaoCriados
            ],
            httpCode: 200
        ))->send();
        exit();
    }
}