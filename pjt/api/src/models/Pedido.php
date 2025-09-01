<?php
declare(strict_types=1);

require_once "Cliente.php";
require_once "Produto.php";

class Pedido implements JsonSerializable
{
    public function __construct(
        private ?int $idpedidos = null,
        private string $data_pedido = "",
        private ?Cliente $cliente = null,
        private ?Produto $produto = null
    ) {
        $this->cliente = $cliente ?? new Cliente();
        $this->produto = $produto ?? new Produto();
    }

    public function jsonSerialize(): array
    {
        return [
            'idpedidos' => $this->idpedidos,
            'data_pedido' => $this->data_pedido,
            'cliente' => $this->cliente->jsonSerialize(),
            'produto' => $this->produto->jsonSerialize()
        ];
    }

    public function getIdPedido(): ?int
    {
        return $this->idpedidos;
    }

    public function setIdPedido(int $idpedidos): self
    {
        $this->idpedidos = $idpedidos;
        return $this;
    }

    public function getDataPedido(): string
    {
        return $this->data_pedido;
    }

    public function setDataPedido(string $data_pedido): self
    {
        $this->data_pedido = $data_pedido;
        return $this;
    }

    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    public function getProduto(): Produto
    {
        return $this->produto;
    }

    public function setProduto(Produto $produto): self
    {
        $this->produto = $produto;
        return $this;
    }
}