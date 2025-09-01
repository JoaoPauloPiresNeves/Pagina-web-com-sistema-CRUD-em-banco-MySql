<?php
declare(strict_types=1);

class Produto implements JsonSerializable
{
    public function __construct(
        private ?int $idprodutos = null,
        private string $nome_produto = "",
        private float $preco_produto = 0.0
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'idprodutos' => $this->idprodutos,
            'nome_produto' => $this->nome_produto,
            'preco_produto' => $this->preco_produto
        ];
    }

    public function getIdProduto(): ?int
    {
        return $this->idprodutos;
    }

    public function setIdProduto(int $idprodutos): self
    {
        $this->idprodutos = $idprodutos;
        return $this;
    }

    public function getNomeProduto(): string
    {
        return $this->nome_produto;
    }

    public function setNomeProduto(string $nome_produto): self
    {
        $this->nome_produto = $nome_produto;
        return $this;
    }

    public function getPrecoProduto(): float
    {
        return $this->preco_produto;
    }

    public function setPrecoProduto(float $preco_produto): self
    {
        $this->preco_produto = $preco_produto;
        return $this;
    }
}