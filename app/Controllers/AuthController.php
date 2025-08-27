<?php

class AuthController {

    /**
     * Exibe a página de login.
     * Esta função é chamada quando o utilizador acede à rota /auth/login.
     */
    public function login() {
        // Simplesmente carrega o ficheiro da view correspondente.
        require_once APP_PATH . 'Views/login.php';
    }

    /**
     * Exibe a página de registo de um novo utilizador.
     * Esta função é chamada quando o utilizador acede à rota /auth/register.
     */
    public function register() {
        // Carrega o ficheiro da view para o formulário de registo.
        require_once APP_PATH . 'Views/cadastro.php';
    }
}