
# API RESTful - CRUD de Cargo e Funcionário

Esta é uma API RESTful didática construída para fins de aprendizado. A API implementa conceitos de **REST** e **MVC**, permitindo realizar as operações **CRUD** (Criar, Ler, Atualizar e Deletar) nas tabelas **Cargo** e **Funcionário**.

A API foi projetada de forma simples e eficiente, com o objetivo de ensinar como estruturar uma API usando o padrão **MVC** e **principais conceitos REST**.

### Recursos:
- CRUD completo para **Cargo** e **Funcionário**.
- Utiliza **REST** para comunicação.
- Implementação simples de **MVC**.
- Foco no aprendizado e na simplicidade de implementação.

---

## 🚀 Funcionalidades

- **GET /cargos**: Lista todos os cargos.
- **GET /cargos/{id}**: Retorna um cargo específico.
- **POST /cargos**: Cria um novo cargo.
- **PUT /cargos/{id}**: Atualiza um cargo existente.
- **DELETE /cargos/{id}**: Deleta um cargo.
  
- **GET /funcionarios**: Lista todos os funcionários.
- **GET /funcionarios/{id}**: Retorna um funcionário específico.
- **POST /funcionarios**: Cria um novo funcionário.
- **PUT /funcionarios/{id}**: Atualiza um funcionário existente.
- **DELETE /funcionarios/{id}**: Deleta um funcionário.

---

## 🛠️ Tecnologias Utilizadas

- **PHP 8.x ou superior**
- **PDO** para interação com o banco de dados.
- **MySQL/MariaDB** para persistência de dados.
- **MVC** para organização do código.
- **REST** para a estrutura da API.
  
---

## 📋 Requisitos

- PHP 8.x ou superior.
- Banco de dados MySQL ou MariaDB.


## 💻 Instalação

1. Clone este repositório:

   ```bash
   git clone https://github.com/helioesperidiao/api-cargo-funcionario.git
   ```

2. Navegue até a pasta do projeto:

   ```bash
   cd api-cargo-funcionario
   ```

3. Configure as credenciais do banco de dados no arquivo `.env` ou no arquivo de configuração de conexão (dependendo da sua implementação).

4. Crie o banco de dados e as tabelas necessárias. Um exemplo de estrutura SQL está disponível na pasta `db/`.

5. Caso necessário, instale as dependências com o **Composer**:

   ```bash
   composer install
   ```

6. Inicie o servidor PHP:

   ```bash
   php -S localhost:8000 -t public/
   ```

---

## 🔑 Endpoints

### Cargo

- **GET /cargos**
  - Descrição: Lista todos os cargos.
  - Resposta:
    ```json
    [
      {
        "idCargo": 1,
        "nomeCargo": "Desenvolvedor"
      }
    ]
    ```

- **GET /cargos/{id}**
  - Descrição: Retorna um cargo específico.
  - Parâmetros:
    - `id` (int): ID do cargo.
  - Resposta:
    ```json
            {
                "success": true,
                "message": "Cargo encontrado com sucesso",
                "data": {
                    "cargos": {
                        "idCargo": 1,
                        "nomeCargo": "Administrador"
                    }
                }
            }
    ```

- **POST /cargos**
  - Descrição: Cria um novo cargo.
  - Corpo da requisição:
    ```json
    {
      "nomeCargo": "Designer"
    }
    ```
  - Resposta:
    ```json
    {
      "idCargo": 2,
      "nomeCargo": "Designer"
    }
    ```

- **PUT /cargos/{id}**
  - Descrição: Atualiza um cargo existente.
  - Parâmetros:
    - `id` (int): ID do cargo.
  - Corpo da requisição:
    ```json
    {
      "nomeCargo": "Gerente de TI"
    }
    ```

- **DELETE /cargos/{id}**
  - Descrição: Deleta um cargo específico.
  - Parâmetros:
    - `id` (int): ID do cargo.

---

### Funcionário

- **GET /funcionarios**
  - Descrição: Lista todos os funcionários.
  - Resposta:
    ```json
    [
      {
        "idFuncionario": 1,
        "nomeFuncionario": "João Silva",
        "idCargo": 1
      }
    ]
    ```

- **GET /funcionarios/{id}**
  - Descrição: Retorna um funcionário específico.
  - Parâmetros:
    - `id` (int): ID do funcionário.
  - Resposta:
    ```json
    {
      "idFuncionario": 1,
      "nomeFuncionario": "João Silva",
      "idCargo": 1
    }
    ```

- **POST /funcionarios**
  - Descrição: Cria um novo funcionário.
  - Corpo da requisição:
    ```json
    {
      "nomeFuncionario": "Maria Oliveira",
      "idCargo": 2
    }
    ```
  - Resposta:
    ```json
    {
      "idFuncionario": 2,
      "nomeFuncionario": "Maria Oliveira",
      "idCargo": 2
    }
    ```

- **PUT /funcionarios/{id}**
  - Descrição: Atualiza um funcionário existente.
  - Parâmetros:
    - `id` (int): ID do funcionário.
  - Corpo da requisição:
    ```json
    {
      "nomeFuncionario": "Maria Souza",
      "idCargo": 3
    }
    ```

- **DELETE /funcionarios/{id}**
  - Descrição: Deleta um funcionário específico.
  - Parâmetros:
    - `id` (int): ID do funcionário.

---

## 🎥 Playlist no YouTube

Para aprender a desenvolver esta API do zero, acesse a playlist de desenvolvimento no **YouTube**:

[Desenvolvimento da API CRUD no YouTube](https://www.youtube.com/playlist?list=PLpdOJd7P4_HsiLH8b5uyFAaaox4r547qe)

---

## 📜 Licença

Esta API é licenciada sob a **MIT License**.

---

Se você tiver dúvidas ou sugestões, sinta-se à vontade para **abrir um problema** ou **contribuir**!
