<?php
require_once "api/src/models/Produto.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";

class ProdutoDAO
{
    public function create(Produto $produto): Produto
    {
        $idProduto = $produto->getIdProduto();
        if (isset($idProduto)) {
            return $this->createWithId(produto: $produto);
        } else {
            return $this->createWithoutId(produto: $produto);
        }
    }

    private function createWithoutId(Produto $produto): Produto
    {
        $query = 'INSERT INTO produtos (nome_produto, preco_produto) VALUES (:nome, :preco)';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':nome', $produto->getNomeProduto(), PDO::PARAM_STR);
        $statement->bindValue(':preco', $produto->getPrecoProduto());
        $statement->execute();
        $produto->setIdProduto((int) Database::getConnection()->lastInsertId());
        return $produto;
    }

    private function createWithId(Produto $produto): Produto
    {
        $query = 'INSERT INTO produtos (idprodutos, nome_produto, preco_produto) VALUES (:id, :nome, :preco)';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $produto->getIdProduto(), PDO::PARAM_INT);
        $statement->bindValue(':nome', $produto->getNomeProduto(), PDO::PARAM_STR);
        $statement->bindValue(':preco', $produto->getPrecoProduto());
        $statement->execute();
        return $produto;
    }

    public function delete(int $idProduto): bool
    {
        $query = 'DELETE FROM produtos WHERE idprodutos = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idProduto, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function readAll(): array
    {
        $resultados = [];
        $query = 'SELECT idprodutos, nome_produto, preco_produto FROM produtos ORDER BY nome_produto ASC';
        $statement = Database::getConnection()->query($query);
        while ($linha = $statement->fetch(PDO::FETCH_OBJ)) {
            $produto = (new Produto())
                ->setIdProduto($linha->idprodutos)
                ->setNomeProduto($linha->nome_produto)
                ->setPrecoProduto($linha->preco_produto);
            $resultados[] = $produto;
        }
        return $resultados;
    }

    public function readByName(string $nomeProduto): Produto|null
    {
        $query = 'SELECT idprodutos, nome_produto, preco_produto FROM produtos WHERE nome_produto = :nome';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':nome', $nomeProduto, PDO::PARAM_STR);
        $statement->execute();
        $objStdProduto = $statement->fetch(PDO::FETCH_OBJ);
        if (!$objStdProduto) {
            return null;
        }
        return (new Produto())
            ->setIdProduto($objStdProduto->idprodutos)
            ->setNomeProduto($objStdProduto->nome_produto)
            ->setPrecoProduto($objStdProduto->preco_produto);
    }

    public function readByPage(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $query = 'SELECT idprodutos, nome_produto, preco_produto FROM produtos ORDER BY nome_produto ASC LIMIT :limit OFFSET :offset';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $resultados = [];
        while ($stdLinha = $statement->fetch(PDO::FETCH_OBJ)) {
            $produto = (new Produto())
                ->setIdProduto($stdLinha->idprodutos)
                ->setNomeProduto($stdLinha->nome_produto)
                ->setPrecoProduto($stdLinha->preco_produto);
            $resultados[] = $produto;
        }
        return $resultados;
    }

    public function readById(int $idProduto): array
    {
        $resultados = [];
        $query = 'SELECT idprodutos, nome_produto, preco_produto FROM produtos WHERE idprodutos = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idProduto, PDO::PARAM_INT);
        $statement->execute();
        $linha = $statement->fetch(PDO::FETCH_OBJ);
        if (!$linha) {
            return [];
        } else {
            $produto = (new Produto())
                ->setIdProduto($linha->idprodutos)
                ->setNomeProduto($linha->nome_produto)
                ->setPrecoProduto($linha->preco_produto);
            return [$produto];
        }
    }

    public function update(Produto $produto): bool
    {
        $query = 'UPDATE produtos SET nome_produto = :nome, preco_produto = :preco WHERE idprodutos = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':nome', $produto->getNomeProduto(), PDO::PARAM_STR);
        $statement->bindValue(':preco', $produto->getPrecoProduto());
        $statement->bindValue(':id', $produto->getIdProduto(), PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }
}