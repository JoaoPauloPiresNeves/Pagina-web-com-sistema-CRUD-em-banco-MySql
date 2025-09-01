<?php
declare(strict_types=1);

class Usuario implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private string $email = "",
        private string $senha = ""
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail()
        ];
    }

    public function getId(): ?int { return $this->id; }
    public function setId($id): self { $this->id = $id; return $this; }
    
    public function getEmail(): string { return $this->email; }
    public function setEmail($email): self { $this->email = $email; return $this; }
    
    public function getSenha(): string { return $this->senha; }
    public function setSenha($senha): self { $this->senha = $senha; return $this; }
}