<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="carrinho-content container">
    <h2>Seu Carrinho de Compras</h2>
    <div id="carrinho-itens-container" class="carrinho-itens-grid">
        </div>
    <div id="carrinho-resumo-container" class="carrinho-resumo" style="display: none;">
        <h3>Total: R$ <span id="carrinho-total">0.00</span></h3>
        <p class="tipo-entrega-info">Opção de Entrega: Retirada no Local</p>
        <input type="hidden" name="tipo_entrega" value="retirada" id="tipo-entrega-retirada">
        <button id="finalizar-pedido" class="btn-cta">Finalizar Pedido</button>
    </div>
</main>

<script>
// A lógica do carrinho no seu JS continuará a funcionar.
// Apenas garanta que o fetch para finalizar pedido aponte para API_BASE_URL + '/pedidos'
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>