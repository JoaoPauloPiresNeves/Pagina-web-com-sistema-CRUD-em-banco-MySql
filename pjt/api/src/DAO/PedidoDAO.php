<?php
require_once "api/src/models/Pedido.php";
require_once "api/src/models/Cliente.php";
require_once "api/src/models/Produto.php";
require_once "api/src/db/Database.php";
require_once "api/src/utils/Logger.php";

class PedidoDAO
{
    public function create(Pedido $pedido): Pedido
    {
        $idPedido = $pedido->getIdPedido();
        if (isset($idPedido)) {
            return $this->createWithId(pedido: $pedido);
        } else {
            return $this->createWithoutId(pedido: $pedido);
        }
    }

    private function createWithoutId(Pedido $pedido): Pedido
    {
        $query = 'INSERT INTO pedidos (data_pedido, clientes_idclientes, produtos_idprodutos) 
                 VALUES (:data, :cliente_id, :produto_id)';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':data', $pedido->getDataPedido());
        $statement->bindValue(':cliente_id', $pedido->getCliente()->getIdCliente(), PDO::PARAM_INT);
        $statement->bindValue(':produto_id', $pedido->getProduto()->getIdProduto(), PDO::PARAM_INT);
        $statement->execute();
        $pedido->setIdPedido((int) Database::getConnection()->lastInsertId());
        return $pedido;
    }

    private function createWithId(Pedido $pedido): Pedido
    {
        $query = 'INSERT INTO pedidos (idpedidos, data_pedido, clientes_idclientes, produtos_idprodutos) 
                 VALUES (:id, :data, :cliente_id, :produto_id)';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $pedido->getIdPedido(), PDO::PARAM_INT);
        $statement->bindValue(':data', $pedido->getDataPedido());
        $statement->bindValue(':cliente_id', $pedido->getCliente()->getIdCliente(), PDO::PARAM_INT);
        $statement->bindValue(':produto_id', $pedido->getProduto()->getIdProduto(), PDO::PARAM_INT);
        $statement->execute();
        return $pedido;
    }

    public function delete(int $idPedido): bool
    {
        $query = 'DELETE FROM pedidos WHERE idpedidos = :id';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function readAll(): array
    {
        $resultados = [];
        $query = 'SELECT p.idpedidos, p.data_pedido, 
                 c.idclientes, c.nome_cliente, c.email_cliente,
                 pr.idprodutos, pr.nome_produto, pr.preco_produto
                 FROM pedidos p
                 JOIN clientes c ON p.clientes_idclientes = c.idclientes
                 JOIN produtos pr ON p.produtos_idprodutos = pr.idprodutos
                 ORDER BY p.data_pedido DESC';
        
        $statement = Database::getConnection()->query($query);
        while ($linha = $statement->fetch(PDO::FETCH_OBJ)) {
            $cliente = (new Cliente())
                ->setIdCliente($linha->idclientes)
                ->setNomeCliente($linha->nome_cliente)
                ->setEmailCliente($linha->email_cliente);
            
            $produto = (new Produto())
                ->setIdProduto($linha->idprodutos)
                ->setNomeProduto($linha->nome_produto)
                ->setPrecoProduto($linha->preco_produto);
            
            $pedido = (new Pedido())
                ->setIdPedido($linha->idpedidos)
                ->setDataPedido($linha->data_pedido)
                ->setCliente($cliente)
                ->setProduto($produto);
            
            $resultados[] = $pedido;
        }
        return $resultados;
    }

    public function readByPage(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $query = 'SELECT p.idpedidos, p.data_pedido, 
                 c.idclientes, c.nome_cliente, c.email_cliente,
                 pr.idprodutos, pr.nome_produto, pr.preco_produto
                 FROM pedidos p
                 JOIN clientes c ON p.clientes_idclientes = c.idclientes
                 JOIN produtos pr ON p.produtos_idprodutos = pr.idprodutos
                 ORDER BY p.data_pedido DESC
                 LIMIT :limit OFFSET :offset';
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        
        $resultados = [];
        while ($linha = $statement->fetch(PDO::FETCH_OBJ)) {
            $cliente = (new Cliente())
                ->setIdCliente($linha->idclientes)
                ->setNomeCliente($linha->nome_cliente)
                ->setEmailCliente($linha->email_cliente);
            
            $produto = (new Produto())
                ->setIdProduto($linha->idprodutos)
                ->setNomeProduto($linha->nome_produto)
                ->setPrecoProduto($linha->preco_produto);
            
            $pedido = (new Pedido())
                ->setIdPedido($linha->idpedidos)
                ->setDataPedido($linha->data_pedido)
                ->setCliente($cliente)
                ->setProduto($produto);
            
            $resultados[] = $pedido;
        }
        return $resultados;
    }

    public function readById(int $idPedido): array
    {
        $resultados = [];
        $query = 'SELECT p.idpedidos, p.data_pedido, 
                 c.idclientes, c.nome_cliente, c.email_cliente,
                 pr.idprodutos, pr.nome_produto, pr.preco_produto
                 FROM pedidos p
                 JOIN clientes c ON p.clientes_idclientes = c.idclientes
                 JOIN produtos pr ON p.produtos_idprodutos = pr.idprodutos
                 WHERE p.idpedidos = :id';
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $statement->execute();
        
        $linha = $statement->fetch(PDO::FETCH_OBJ);
        if (!$linha) {
            return [];
        } else {
            $cliente = (new Cliente())
                ->setIdCliente($linha->idclientes)
                ->setNomeCliente($linha->nome_cliente)
                ->setEmailCliente($linha->email_cliente);
            
            $produto = (new Produto())
                ->setIdProduto($linha->idprodutos)
                ->setNomeProduto($linha->nome_produto)
                ->setPrecoProduto($linha->preco_produto);
            
            $pedido = (new Pedido())
                ->setIdPedido($linha->idpedidos)
                ->setDataPedido($linha->data_pedido)
                ->setCliente($cliente)
                ->setProduto($produto);
            
            return [$pedido];
        }
    }

    public function update(Pedido $pedido): bool
    {
        $query = 'UPDATE pedidos SET 
                 data_pedido = :data,
                 clientes_idclientes = :cliente_id,
                 produtos_idprodutos = :produto_id
                 WHERE idpedidos = :id';
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':data', $pedido->getDataPedido());
        $statement->bindValue(':cliente_id', $pedido->getCliente()->getIdCliente(), PDO::PARAM_INT);
        $statement->bindValue(':produto_id', $pedido->getProduto()->getIdProduto(), PDO::PARAM_INT);
        $statement->bindValue(':id', $pedido->getIdPedido(), PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }
}