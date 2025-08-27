<?php
class ContaController {
    public function index() {
        // A lógica de verificação de login será feita no JavaScript da view
        require_once APP_PATH . 'Views/minha_conta.php';
    }
}