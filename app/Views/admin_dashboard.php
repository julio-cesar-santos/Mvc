<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="admin-content container">
    <div class="admin-header">
        <h2>Bem-vindo, Administrador!</h2>
        <p>Gerencie o seu laticínio de forma rápida e eficiente a partir deste painel.</p>
    </div>

    <div class="admin-actions">
        <a href="<?php echo BASE_URL; ?>/admin/produtos" class="action-card">
            <i class="fas fa-plus-circle"></i>
            <h3>Gerenciar Produtos</h3>
            <p>Adicione, edite ou remova produtos do seu catálogo.</p>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/faturamento" class="action-card">
            <i class="fas fa-chart-line"></i>
            <h3>Ver Faturamento e Pedidos</h3>
            <p>Acompanhe as vendas e gerencie os pedidos dos clientes.</p>
        </a>
    </div>
</main>

<script>
// A verificação de admin já é feita no AdminController,
// mas pode manter uma verificação extra no JS se preferir.
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>