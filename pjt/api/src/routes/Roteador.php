<?php

require_once "api/src/routes/Router.php"; 
require_once "api/src/controllers/ClienteControl.php";
require_once "api/src/middlewares/ClienteMiddleware.php";

require_once "api/src/controllers/ProdutoControl.php";
require_once "api/src/middlewares/ProdutoMiddleware.php";

require_once "api/src/controllers/PedidoControl.php";
require_once "api/src/middlewares/PedidoMiddleware.php";

require_once "api/src/middlewares/LoginMiddleware.php";
require_once "api/src/controllers/LoginControl.php";

require_once "api/src/utils/MeuTokenJWT.php";


class Roteador
{
    public function __construct(private Router $router = new Router())
    {
        $this->router = new Router();

        $this->setupHeaders();
        $this->setupClienteRoutes();
        $this->setupProdutoRoutes();
        $this->setupPedidoRoutes();
        $this->setupBackupRoutes();
        $this->setupLoginRoutes();
        $this->setup404Route();
    }

    private function setup404Route(): void
    {
        $this->router->set404(function (): void {
            header('Content-Type: application/json');
            (new Response(
                success: false,
                message: "Rota não encontrada",
                error: [
                    'code' => 'routing_error', 
                    'message' => 'Rota não mapeada'  
                ],
                httpCode: 404 
            ))->send();
        });
    }

    private function setupHeaders(): void
    {
        header(header: 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header(header: 'Access-Control-Allow-Origin: *');
        header(header: 'Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    private function sendErrorResponse(Throwable $throwable, string $message): never
    {
        Logger::Log(throwable: $throwable);
        (new Response(
            success: false,
            message: $message,
            error: [
                'code' => $throwable->getCode(), 
                'message' => $throwable->getMessage() 
            ],
            httpCode: 500  
        ))->send();

        exit();
    }

    private function setupClienteRoutes(): void
    {
        $this->router->get(pattern: '/clientes', fn: function (): never {
            try {
                $clienteControl = new ClienteControl();
                if ((isset($_GET['page'])) && isset($_GET['limit'])) {
                    $page = $_GET['page'];   
                    $limit = $_GET['limit']; 
                    (new ClienteMiddleware())
                        ->isValidPage(page: $page) 
                        ->isValidLimit(limit: $limit); 
                    $clienteControl->listPaginated(page: $page, limit: $limit);
                } else {
                    $clienteControl->index();
                }
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na seleção de clientes'
                );
            }
            exit();
        });

        $this->router->get(pattern: "/clientes/(\d+)", fn: function ($idCliente): never {
            try {
                $clienteMiddleware = new ClienteMiddleware();
                $clienteMiddleware->isValidId(idCliente: $idCliente); 
                $clienteControl = new ClienteControl();
                $clienteControl->show(idCliente: $idCliente);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable, 
                    message: 'Erro na seleção do cliente' 
                );
            }
            exit();
        });

        $this->router->post(pattern: "/clientes", fn: function (): never {
            try {
                $requestBody = file_get_contents(filename: "php://input");
                $clienteMiddleware = new ClienteMiddleware();
                $stdCliente = $clienteMiddleware->stringJsonToStdClass(requestBody: $requestBody);
                $clienteMiddleware
                    ->isValidNomeCliente(nomeCliente: $stdCliente->cliente->nome_cliente) 
                    ->isValidEmail(email: $stdCliente->cliente->email_cliente)
                    ->hasNotClienteByEmail(email: $stdCliente->cliente->email_cliente); 
                $clienteControl = new ClienteControl();
                $clienteControl->store(stdCliente: $stdCliente); 
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable, 
                    message: 'Erro ao inserir um novo cliente' 
                );
            }
            exit();
        });

        $this->router->put(pattern: "/clientes/(\d+)", fn: function ($id): never {
            try {
                $requestBody = file_get_contents(filename: "php://input");
                $clienteMiddleware = new ClienteMiddleware();
                $stdCliente = $clienteMiddleware->stringJsonToStdClass(requestBody: $requestBody);
                $clienteMiddleware
                    ->isValidId(idCliente: $id)
                    ->isValidNomeCliente(nomeCliente: $stdCliente->cliente->nome_cliente)
                    ->isValidEmail(email: $stdCliente->cliente->email_cliente);
                $stdCliente->cliente->idclientes = $id;
                $clienteControl = new ClienteControl();
                $clienteControl->edit(stdCliente: $stdCliente);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na atualização do cliente'
                );
            }
            exit();
        });

        $this->router->delete(pattern: "/clientes/(\d+)", fn: function ($idCliente): never {
            try {
                $clienteMiddleware = new ClienteMiddleware();
                $clienteMiddleware->isValidId(idCliente: $idCliente);
                $clienteControl = new ClienteControl();
                $clienteControl->destroy(idCliente: $idCliente);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na exclusão do cliente'
                );
            }
            exit();
        });
    }

    private function setupProdutoRoutes(): void
    {
        $this->router->get(pattern: '/produtos', fn: function (): never {
            try {
                $produtoControl = new ProdutoControl();
                if ((isset($_GET['page'])) && isset($_GET['limit'])) {
                    $page = $_GET['page'];   
                    $limit = $_GET['limit']; 
                    (new ProdutoMiddleware())
                        ->isValidPage(page: $page) 
                        ->isValidLimit(limit: $limit); 
                    $produtoControl->listPaginated(page: $page, limit: $limit);
                } else {
                    $produtoControl->index();
                }
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na seleção de produtos'
                );
            }
            exit();
        });

        $this->router->get(pattern: "/produtos/(\d+)", fn: function ($idProduto): never {
            try {
                $produtoMiddleware = new ProdutoMiddleware();
                $produtoMiddleware->isValidId(idProduto: $idProduto); 
                $produtoControl = new ProdutoControl();
                $produtoControl->show(idProduto: $idProduto);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable, 
                    message: 'Erro na seleção do produto' 
                );
            }
            exit();
        });

        $this->router->post(pattern: "/produtos", fn: function (): never {
            try {
                $requestBody = file_get_contents(filename: "php://input");
                $produtoMiddleware = new ProdutoMiddleware();
                $stdProduto = $produtoMiddleware->stringJsonToStdClass(requestBody: $requestBody);
                $produtoMiddleware
                    ->isValidNomeProduto(nomeProduto: $stdProduto->produto->nome_produto) 
                    ->isValidPreco(preco: $stdProduto->produto->preco_produto)
                    ->hasNotProdutoByName(nomeProduto: $stdProduto->produto->nome_produto); 
                $produtoControl = new ProdutoControl();
                $produtoControl->store(stdProduto: $stdProduto); 
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable, 
                    message: 'Erro ao inserir um novo produto' 
                );
            }
            exit();
        });

        $this->router->put(pattern: "/produtos/(\d+)", fn: function ($id): never {
            try {
                $requestBody = file_get_contents(filename: "php://input");
                $produtoMiddleware = new ProdutoMiddleware();
                $stdProduto = $produtoMiddleware->stringJsonToStdClass(requestBody: $requestBody);
                $produtoMiddleware
                    ->isValidId(idProduto: $id)
                    ->isValidNomeProduto(nomeProduto: $stdProduto->produto->nome_produto)
                    ->isValidPreco(preco: $stdProduto->produto->preco_produto);
                $stdProduto->produto->idprodutos = $id;
                $produtoControl = new ProdutoControl();
                $produtoControl->edit(stdProduto: $stdProduto);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na atualização do produto'
                );
            }
            exit();
        });

        $this->router->delete(pattern: "/produtos/(\d+)", fn: function ($idProduto): never {
            try {
                $produtoMiddleware = new ProdutoMiddleware();
                $produtoMiddleware->isValidId(idProduto: $idProduto);
                $produtoControl = new ProdutoControl();
                $produtoControl->destroy(idProduto: $idProduto);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na exclusão do produto'
                );
            }
            exit();
        });
    }

    private function setupPedidoRoutes(): void
    {
        $this->router->get(pattern: '/pedidos', fn: function (): never {
            try {
                $pedidoControl = new PedidoControl();
                if ((isset($_GET['page'])) && isset($_GET['limit'])) {
                    $page = $_GET['page'];   
                    $limit = $_GET['limit']; 
                    (new PedidoMiddleware())
                        ->isValidPage(page: $page) 
                        ->isValidLimit(limit: $limit); 
                    $pedidoControl->listPaginated(page: $page, limit: $limit);
                } else {
                    $pedidoControl->index();
                }
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na seleção de pedidos'
                );
            }
            exit();
        });

        $this->router->get(pattern: "/pedidos/(\d+)", fn: function ($idPedido): never {
            try {
                $pedidoMiddleware = new PedidoMiddleware();
                $pedidoMiddleware->isValidId(idPedido: $idPedido); 
                $pedidoControl = new PedidoControl();
                $pedidoControl->show(idPedido: $idPedido);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable, 
                    message: 'Erro na seleção do pedido' 
                );
            }
            exit();
        });

        $this->router->post(pattern: "/pedidos", fn: function (): never {
            try {
                $requestBody = file_get_contents(filename: "php://input");
                $pedidoMiddleware = new PedidoMiddleware();
                $stdPedido = $pedidoMiddleware->stringJsonToStdClass(requestBody: $requestBody);
                $pedidoMiddleware
                    ->isValidDataPedido(dataPedido: $stdPedido->pedido->data_pedido)
                    ->isValidClienteId(clienteId: $stdPedido->pedido->clientes_idclientes)
                    ->isValidProdutoId(produtoId: $stdPedido->pedido->produtos_idprodutos);
                $pedidoControl = new PedidoControl();
                $pedidoControl->store(stdPedido: $stdPedido);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable, 
                    message: 'Erro ao inserir um novo pedido' 
                );
            }
            exit();
        });

        $this->router->put(pattern: "/pedidos/(\d+)", fn: function ($id): never {
            try {
                $requestBody = file_get_contents(filename: "php://input");
                $pedidoMiddleware = new PedidoMiddleware();
                $stdPedido = $pedidoMiddleware->stringJsonToStdClass(requestBody: $requestBody);
                $pedidoMiddleware
                    ->isValidId(idPedido: $id)
                    ->isValidDataPedido(dataPedido: $stdPedido->pedido->data_pedido)
                    ->isValidClienteId(clienteId: $stdPedido->pedido->clientes_idclientes)
                    ->isValidProdutoId(produtoId: $stdPedido->pedido->produtos_idprodutos);
                $stdPedido->pedido->idpedidos = $id;
                $pedidoControl = new PedidoControl();
                $pedidoControl->edit(stdPedido: $stdPedido);
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(
                    throwable: $throwable,
                    message: 'Erro na atualização do pedido'
                );
            }
            exit();
        });

        $this->router->delete(pattern: "/pedidos/(\d+)", fn: function ($idPedido): never {
        try {
            $pedidoMiddleware = new PedidoMiddleware();
            $pedidoMiddleware->isValidId(idPedido: $idPedido);
            $pedidoControl = new PedidoControl();
            $pedidoControl->destroy(idPedido: $idPedido);
        } catch (Throwable $throwable) {
            $this->sendErrorResponse(
                throwable: $throwable,
                message: 'Erro na exclusão do pedido'
            );
        }
        exit(); // ← O exit() deve estar DENTRO da função
    });    
    // ← Parênteses de fechamento corretos


    
}








private function setupLoginRoutes()
{
    $this->router->post('/logar', function (): never {
        try {
            $requestBody = file_get_contents("php://input");
            $loginMiddleware = new LoginMiddleware();
            
            $stdLogin = $loginMiddleware->stringJsonToStdClass($requestBody);
            $loginMiddleware
                ->isValidEmail($stdLogin->usuario->email)
                ->isvalidSenha($stdLogin->usuario->senha);

            $loginControl = new LoginControl();
            $loginControl->autenticar($stdLogin);

        } catch (Throwable $throwable) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao efetuar login',
                'error' => ['message' => $throwable->getMessage()]
            ], JSON_PRETTY_PRINT);
        }

        exit();
    });
}


         
    

    public function setupBackupRoutes(): void
    {
        $this->router->get(pattern: '/backup', fn: function (): never {
            try {
                require_once "api/src/db/Database.php";
                Database::backup();
            } catch (Throwable $throwable) {
                $this->sendErrorResponse(throwable: $throwable, message: 'Erro ao realizar backup');
            }
            exit();
        });
    }

    public function start(): void
    {
        $this->router->run();
    }
}