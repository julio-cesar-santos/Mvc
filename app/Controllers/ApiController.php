<?php
class ApiController {

    // Função auxiliar para enviar respostas JSON padronizadas
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit();
    }

    // Funções auxiliares de verificação
    private function checkAdmin() {
        if (!isAdmin()) {
            $this->jsonResponse(['message' => 'Acesso negado. Apenas administradores podem realizar esta ação.'], 403);
        }
    }
    private function checkAuth() {
        if (!isAuthenticated()) {
            $this->jsonResponse(['message' => 'Não autorizado. Faça login para continuar.'], 401);
        }
    }

    /**
     * Rota: /api/authStatus
     * Substitui: auth_status.php e check_admin.php
     */
    public function authStatus() {
        $response = [
            'isAuthenticated' => isAuthenticated(),
            'isAdmin' => isAdmin()
        ];
        if ($response['isAuthenticated']) {
            $response['user_id'] = $_SESSION['user_id'];
            $response['user_name'] = $_SESSION['user_name'];
        }
        $this->jsonResponse($response);
    }

    /**
     * Rota: /api/register (Método: POST)
     * Substitui: register_process.php
     */
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        $nome = $data['nome'] ?? '';
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $this->jsonResponse(['message' => 'Por favor, preencha todos os campos.'], 400);
        }

        $userModel = new Usuario();
        if ($userModel->create($nome, $email, $senha)) {
            $this->jsonResponse(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao cadastrar. O email pode já estar em uso.'], 409);
        }
    }

    /**
     * Rota: /api/login (Método: POST)
     * Substitui: login_process.php
     */
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $senha = $data['senha'] ?? '';

        $userModel = new Usuario();
        $user = $userModel->login($email, $senha);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            
            $this->jsonResponse([
                'success' => true, 'message' => 'Login realizado com sucesso!',
                'user_id' => $user['id'], 'is_admin' => (bool)$user['is_admin'], 'user_name' => $user['nome']
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Email ou senha inválidos.'], 401);
        }
    }

    /**
     * Rota: /api/logout (Método: POST)
     * Substitui: logout.php
     */
    public function logout() {
        session_unset();
        session_destroy();
        $this->jsonResponse(['success' => true, 'message' => 'Sessão encerrada com sucesso!']);
    }

    /**
     * Rota: /api/produtos (Métodos: GET, POST, DELETE)
     * Rota: /api/produtos/update (Método: POST com _method=PUT)
     * Substitui: produtos_api.php
     */
    public function produtos() {
        $method = $_SERVER['REQUEST_METHOD'];
        $produtoModel = new Produto();

        switch ($method) {
            case 'GET':
                // Listagem de produtos é pública
                $this->jsonResponse($produtoModel->getAll());
                break;
            
            case 'POST':
                $this->checkAdmin(); // Apenas admin pode criar/atualizar
                if (isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
                    $this->updateProduto();
                } else {
                    $this->createProduto();
                }
                break;

            case 'DELETE':
                $this->checkAdmin(); // Apenas admin pode excluir
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? 0;
                if (!$id) $this->jsonResponse(['message' => 'ID do produto é obrigatório.'], 400);

                if ($produtoModel->delete($id)) {
                    $this->jsonResponse(['message' => 'Produto excluído com sucesso!']);
                } else {
                    $this->jsonResponse(['message' => 'Erro ao excluir. O produto pode estar associado a um pedido.'], 500);
                }
                break;
            
            default:
                $this->jsonResponse(['message' => 'Método não permitido'], 405);
        }
    }

    private function createProduto() {
        $nome = $_POST['nome'] ?? '';
        $preco = $_POST['preco'] ?? 0;
        $estoque = $_POST['estoque'] ?? 0;
        $imagem = isset($_FILES['imagem']) ? file_get_contents($_FILES['imagem']['tmp_name']) : null;

        if (empty($nome) || empty($preco)) {
            $this->jsonResponse(['message' => 'Nome e preço são obrigatórios.'], 400);
        }

        $produtoModel = new Produto();
        $newId = $produtoModel->create($nome, $preco, $estoque, $imagem);
        $this->jsonResponse(['message' => 'Produto adicionado com sucesso!', 'id' => $newId], 201);
    }
    
    private function updateProduto() {
        $id = $_POST['id'] ?? 0;
        $nome = $_POST['nome'] ?? '';
        $preco = $_POST['preco'] ?? 0;
        $estoque = $_POST['estoque'] ?? 0;
        $imagem = isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK ? file_get_contents($_FILES['imagem']['tmp_name']) : null;

        if (empty($id) || empty($nome) || empty($preco)) {
            $this->jsonResponse(['message' => 'ID, nome e preço são obrigatórios.'], 400);
        }

        $produtoModel = new Produto();
        $produtoModel->update($id, $nome, $preco, $estoque, $imagem);
        $this->jsonResponse(['message' => 'Produto atualizado com sucesso!']);
    }

    /**
     * Rota: /api/produtoImagem/[ID] (Método: GET)
     */
    public function produtoImagem($id) {
        $produtoModel = new Produto();
        $imagem = $produtoModel->getImageById($id);

        header("Content-Type: image/jpeg");
        if ($imagem) {
            echo $imagem;
        } else {
            readfile(APP_PATH . '../public/ico/favicon.ico');
        }
        exit();
    }
    
    public function pedidos() {
        $this->checkAuth();
        $method = $_SERVER['REQUEST_METHOD'];
        $pedidoModel = new Pedido();

        switch ($method) {
            case 'GET':
                if (isAdmin()) {
                    if (isset($_GET['faturamento'])) {
                        $faturamento = $pedidoModel->getFaturamento();
                        $this->jsonResponse($faturamento);
                    } else {
                        $pedidos = $pedidoModel->getAllWithDetails();
                        $this->jsonResponse($pedidos);
                    }
                } else {
                    $pedidos = $pedidoModel->getByUserId($_SESSION['user_id']);
                    $this->jsonResponse($pedidos);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $carrinho = $data['carrinho'] ?? [];
                $tipo_entrega = $data['tipo_entrega'] ?? 'retirada';

                if (empty($carrinho)) {
                    $this->jsonResponse(['message' => 'O carrinho está vazio.'], 400);
                }

                try {
                    $pedidoId = $pedidoModel->create($_SESSION['user_id'], $carrinho, $tipo_entrega);
                    $this->jsonResponse(['message' => 'Pedido realizado com sucesso!', 'pedido_id' => $pedidoId], 201);
                } catch (Exception $e) {
                    $this->jsonResponse(['message' => 'Erro ao realizar pedido: ' . $e->getMessage()], 400);
                }
                break;

            case 'PUT':
                $this->checkAdmin();
                $data = json_decode(file_get_contents('php://input'), true);
                $pedido_id = $data['pedido_id'] ?? 0;
                $status = $data['status'] ?? '';

                if (empty($pedido_id) || !in_array($status, ['pendente', 'processando', 'concluido', 'cancelado'])) {
                    $this->jsonResponse(['message' => 'ID do pedido e status válidos são obrigatórios.'], 400);
                }
                
                try {
                    $pedidoModel->updateStatus($pedido_id, $status);
                    $this->jsonResponse(['message' => 'Status do pedido atualizado com sucesso!']);
                } catch (Exception $e) {
                    $this->jsonResponse(['message' => 'Erro ao atualizar status: ' . $e->getMessage()], 500);
                }
                break;

            default:
                $this->jsonResponse(['message' => 'Método não permitido'], 405);
        }
    }

    /**
     * Rota: /api/gerarRelatorio (Método: GET)
     * Substitui: gerar_relatorio.php
     */
    public function gerarRelatorio() {
        $this->checkAdmin();
        $pedidoModel = new Pedido();
        $pedidos = $pedidoModel->getAllWithDetails();

        $filename = "relatorio_pedidos_" . date('Y-m-d') . ".txt";
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fileContent = "     Relatório de Pedidos - Laticínio Esperança\n";
        $fileContent .= "Gerado em: " . date('d/m/Y H:i:s') . "\n\n";

        if (empty($pedidos)) {
            $fileContent .= "Nenhum pedido encontrado no sistema.";
        } else {
            foreach ($pedidos as $pedido) {
                $fileContent .= "Pedido ID:      " . $pedido['id'] . "\n";
                $fileContent .= "Cliente:        " . $pedido['cliente_nome'] . "\n";
                $fileContent .= "Data do Pedido: " . date('d/m/Y H:i', strtotime($pedido['data_pedido'])) . "\n";
                $fileContent .= "Status:         " . ucfirst($pedido['status']) . "\n";
                $fileContent .= "Valor Total:    R$ " . number_format($pedido['total'], 2, ',', '.') . "\n";
                $fileContent .= "----------------------------------------------------------\n";
            }
        }
        
        echo $fileContent;
        exit();
    }
}