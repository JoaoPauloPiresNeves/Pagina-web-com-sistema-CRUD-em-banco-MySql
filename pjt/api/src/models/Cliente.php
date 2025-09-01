<?php
declare(strict_types=1);

class Cliente implements JsonSerializable
{
    public function __construct(
        private ?int $idclientes = null,
        private string $nome_cliente = "",
        private string $email_cliente = ""
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'idclientes' => $this->idclientes,
            'nome_cliente' => $this->nome_cliente,
            'email_cliente' => $this->email_cliente
        ];
    }

    public function getIdCliente(): ?int
    {
        return $this->idclientes;
    }

    public function setIdCliente(int $idclientes): self
    {
        $this->idclientes = $idclientes;
        return $this;
    }

    public function getNomeCliente(): string
    {
        return $this->nome_cliente;
    }

    public function setNomeCliente(string $nome_cliente): self
    {
        $this->nome_cliente = $nome_cliente;
        return $this;
    }

    public function getEmailCliente(): string
    {
        return $this->email_cliente;
    }

    public function setEmailCliente(string $email_cliente): self
    {
        $this->email_cliente = $email_cliente;
        return $this;
    }
}