<?php
require_once "api/src/models/Cliente.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";

class ClienteDAO
{
    public function create(Cliente $cliente): Cliente
    {
        $idCliente = $cliente->getIdCliente();
        if (isset($idCliente)) {
            return $this->createWithId(cliente: $cliente);
        } else {
            return $this->createWithoutId(cliente: $cliente);
        }
    }

    private function createWithoutId(Cliente $cliente): Cliente
    {
        $query = 'INSERT INTO clientes (nome_cliente, email_cliente) VALUES (:nome, :email)';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':nome', $cliente->getNomeCliente(), PDO::PARAM_STR);
        $statement->bindValue(':email', $cliente->getEmailCliente(), PDO::PARAM_STR);
        $statement->execute();
        $cliente->setIdCliente((int) Database::getConnection()->lastInsertId());
        return $cliente;
    }

    private function createWithId(Cliente $cliente): Cliente
    {
        $query = 'INSERT INTO clientes (idclientes, nome_cliente, email_cliente) VALUES (:id, :nome, :email)';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $cliente->getIdCliente(), PDO::PARAM_INT);
        $statement->bindValue(':nome', $cliente->getNomeCliente(), PDO::PARAM_STR);
        $statement->bindValue(':email', $cliente->getEmailCliente(), PDO::PARAM_STR);
        $statement->execute();
        return $cliente;
    }

    public function delete(int $idCliente): bool
    {
        $query = 'DELETE FROM clientes WHERE idclientes = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idCliente, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function readAll(): array
    {
        $resultados = [];
        $query = 'SELECT idclientes, nome_cliente, email_cliente FROM clientes ORDER BY nome_cliente ASC';
        $statement = Database::getConnection()->query($query);
        while ($linha = $statement->fetch(PDO::FETCH_OBJ)) {
            $cliente = (new Cliente())
                ->setIdCliente($linha->idclientes)
                ->setNomeCliente($linha->nome_cliente)
                ->setEmailCliente($linha->email_cliente);
            $resultados[] = $cliente;
        }
        return $resultados;
    }

    public function readByEmail(string $email): Cliente|null
    {
        $query = 'SELECT idclientes, nome_cliente, email_cliente FROM clientes WHERE email_cliente = :email';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $objStdCliente = $statement->fetch(PDO::FETCH_OBJ);
        if (!$objStdCliente) {
            return null;
        }
        return (new Cliente())
            ->setIdCliente($objStdCliente->idclientes)
            ->setNomeCliente($objStdCliente->nome_cliente)
            ->setEmailCliente($objStdCliente->email_cliente);
    }

    public function readByPage(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $query = 'SELECT idclientes, nome_cliente, email_cliente FROM clientes ORDER BY nome_cliente ASC LIMIT :limit OFFSET :offset';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $resultados = [];
        while ($stdLinha = $statement->fetch(PDO::FETCH_OBJ)) {
            $cliente = (new Cliente())
                ->setIdCliente($stdLinha->idclientes)
                ->setNomeCliente($stdLinha->nome_cliente)
                ->setEmailCliente($stdLinha->email_cliente);
            $resultados[] = $cliente;
        }
        return $resultados;
    }

    public function readById(int $idCliente): array
    {
        $resultados = [];
        $query = 'SELECT idclientes, nome_cliente, email_cliente FROM clientes WHERE idclientes = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idCliente, PDO::PARAM_INT);
        $statement->execute();
        $linha = $statement->fetch(PDO::FETCH_OBJ);
        if (!$linha) {
            return [];
        } else {
            $cliente = (new Cliente())
                ->setIdCliente($linha->idclientes)
                ->setNomeCliente($linha->nome_cliente)
                ->setEmailCliente($linha->email_cliente);
            return [$cliente];
        }
    }

    public function update(Cliente $cliente): bool
    {
        $query = 'UPDATE clientes SET nome_cliente = :nome, email_cliente = :email WHERE idclientes = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':nome', $cliente->getNomeCliente(), PDO::PARAM_STR);
        $statement->bindValue(':email', $cliente->getEmailCliente(), PDO::PARAM_STR);
        $statement->bindValue(':id', $cliente->getIdCliente(), PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }
}