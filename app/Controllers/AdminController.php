<?php
class AdminController {
    public function __construct() {
        // Protege todas as rotas de admin
        if (!isAdmin()) {
            // Redireciona para o login se não for admin
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }

    public function index() {
        // Rota principal do admin, carrega o dashboard
        require_once APP_PATH . 'Views/admin_dashboard.php';
    }

    public function produtos() {
        require_once APP_PATH . 'Views/admin_add_produto.php';
    }

    public function faturamento() {
        require_once APP_PATH . 'Views/admin_faturamento.php';
    }
}