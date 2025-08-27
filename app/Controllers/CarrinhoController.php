<?php
class CarrinhoController {
    public function index() {
        // O carrinho é gerido no frontend, basta carregar a view
        require_once APP_PATH . 'Views/carrinho.php';
    }
}