<?php require_once __DIR__ . '/layouts/header.php'; ?>
<main>
    <div id="produtos-container" class="container">
        <p>A carregar produtos...</p>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // A lógica de fetchProducts() no seu main.js irá funcionar aqui
    // Certifique-se que ela busca de API_BASE_URL + '/produtos'
});
</script>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>