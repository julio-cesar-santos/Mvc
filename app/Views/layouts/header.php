<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laticínio Esperança</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/produtos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/carrinho.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/login.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/minha_conta.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/ico/favicon.ico" type="image/x-icon">
</head>
<body>
    <div id="notification-container"></div>
    <div class="top-bar-info">
        <div class="container">
            <span>Laticínio Esperança</span>
            <div id="auth-cart-area">
                <a href="<?php echo BASE_URL; ?>/auth/login" id="nav-login">Iniciar Sessão</a>
                <a href="<?php echo BASE_URL; ?>/auth/register" id="nav-cadastro">Cadastre-se</a>
                <a href="<?php echo BASE_URL; ?>/carrinho" id="cart-link"> <i class="fas fa-shopping-cart"></i> Carrinho
                    <span class="cart-count-display" style="display: none;">0</span></a>
                <a href="<?php echo BASE_URL; ?>/conta" id="nav-conta" style="display:none;">Minha Conta</a>
                <a href="<?php echo BASE_URL; ?>/admin" id="nav-admin" style="display:none;">Admin</a>
                <a href="#" id="nav-logout" style="display:none;">Sair</a>
            </div>
        </div>
    </div>
    <header class="main-header-content">
        <div class="container logo-container">
            <a href="<?php echo BASE_URL; ?>/home" class="logo-link">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQGeuHdGJbjRIyDGpJehNFmZX9dJ3Sy1CCEWg&s" alt="Logo Laticínio Esperança" class="main-logo-img">
            </a>
            <h1>Laticínio Esperança</h1>
        </div>
        <nav class="main-navigation">
            <div class="container">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/home">Início</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/produto">Produtos</a></li>
                    <li><a href="#contato">Contato</a></li>
                </ul>
            </div>
        </nav>
    </header>