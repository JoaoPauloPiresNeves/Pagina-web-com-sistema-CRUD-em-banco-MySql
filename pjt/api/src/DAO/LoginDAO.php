<?php
require_once "api/src/models/Usuario.php";
require_once "api/src/db/Database.php";

class LoginDAO
{
    public function verificarLogin(Usuario $usuario): array
    {
        $query = 'SELECT id, email, senha FROM logar WHERE email = :email';
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':email', $usuario->getEmail(), PDO::PARAM_STR);
        $statement->execute();

        $linha = $statement->fetch(PDO::FETCH_OBJ);

        if (!$linha) {
            return [];
        }

        if ($usuario->getSenha() !== $linha->senha) {
            return [];
        }

        $usuarioLogado = new Usuario();
        $usuarioLogado
            ->setId($linha->id)
            ->setEmail($linha->email)
            ->setSenha('');

        return [$usuarioLogado];
    }
}