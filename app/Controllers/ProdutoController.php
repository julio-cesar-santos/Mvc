<?php
class ProdutoController {
    public function index() {
        $produtoModel = new Produto();
        $produtos = $produtoModel->getAll(); // Este método precisa ser criado no Model/Produto.php
        require_once APP_PATH . 'Views/produtos.php';
    }
}