<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="admin-content container">
    <div class="admin-header-options">
        <a href="<?php echo BASE_URL; ?>/admin" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>
    </div>

    <div class="faturamento-container list-container">
        <h2>Relatório de Faturamento</h2>
        <div id="faturamento-data">
            </div>
    </div>

    <div class="pedidos-container list-container">
        <div class="admin-header-with-button">
            <h2>Todos os Pedidos</h2>
            <a href="<?php echo BASE_URL; ?>/api/gerarRelatorio" class="btn-cta" target="_blank">
                <i class="fas fa-file-alt"></i> Gerar Relatório TXT
            </a>
        </div>
        <div id="lista-pedidos-admin">
            </div>
    </div>
</main>

<script>
// A lógica JS para esta página deve fazer fetch para API_BASE_URL + '/pedidos'
// e API_BASE_URL + '/pedidos?faturamento=true'
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>