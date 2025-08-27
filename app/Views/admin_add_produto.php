<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="admin-content container">
    <div class="admin-header-options">
        <a href="<?php echo BASE_URL; ?>/admin" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>
    </div>

    <div class="form-container">
        <h2 id="form-title">Adicionar Novo Produto</h2>
        <form id="addProdutoForm" class="admin-form" enctype="multipart/form-data">
            <input type="hidden" id="edit-id" name="id">
            <div class="input-group">
                <label for="nome-produto">Nome do Produto:</label>
                <input type="text" id="nome-produto" name="nome" required>
            </div>
            <div class="input-group">
                <label for="preco-produto">Preço (R$):</label>
                <input type="number" id="preco-produto" name="preco" step="0.01" min="0" required>
            </div>
            <div class="input-group">
                <label for="imagem-produto">Imagem do Produto:</label>
                <input type="file" id="imagem-produto" name="imagem" accept="image/jpeg, image/png, image/gif">
            </div>
            <div class="input-group">
                <label for="estoque-produto">Estoque:</label>
                <input type="number" id="estoque-produto" name="estoque" min="0" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn-cta" id="submit-btn">Adicionar Produto</button>
                <button type="button" class="btn-secondary" id="cancel-edit-btn" style="display: none;">Cancelar Edição</button>
            </div>
        </form>
    </div>

    <div class="list-container">
        <h3>Produtos Existentes</h3>
        <div id="lista-produtos-admin">
            </div>
    </div>
</main>

<script>
// A lógica JS para esta página deve fazer fetch para API_BASE_URL + '/produtos'
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>