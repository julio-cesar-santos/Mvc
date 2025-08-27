<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="forms">
    <form id="cadastroForm">
        <h2>Cadastre-se</h2>

        <div class="input-box">
             <svg xmlns="http://www.w3.org/2000/svg" title="Nome" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                <path fill-rule="evenodd" d="M11.013 2.513a1.75 1.75 0 0 1 2.475 2.474L6.226 12.25a2.751 2.751 0 0 1-.892.596l-2.047.848a.75.75 0 0 1-.98-.98l.848-2.047a2.75 2.75 0 0 1 .596-.892l7.262-7.261Z" clip-rule="evenodd" />
            </svg>
            <input placeholder="Seu nome" type="text" id="nome" required>
        </div>

        <div class="input-box">
             <svg xmlns="http://www.w3.org/2000/svg" title="Login" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" />
            </svg>
            <input placeholder="E-mail" type="email" id="email" required>
        </div>

        <div class="input-box">
            <svg xmlns="http://www.w3.org/2000/svg" title="Senha" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                <path fill-rule="evenodd" d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7V4.5A3.5 3.5 0 0 0 8 1Zm2 6V4.5a2 2 0 1 0-4 0V7h4Z" clip-rule="evenodd" />
            </svg>
            <input placeholder="Senha" type="password" id="senha" required>
        </div>

        <button type="submit"> Cadastrar </button>

        <div class="register">
            <p>Já tem conta? <a href="<?php echo BASE_URL; ?>/auth/login"> Entre agora </a> </p>
        </div>
    </form>
</main>

<script>
// Lembre-se de que a lógica em main.js para este formulário
// deve agora fazer o fetch para API_BASE_URL + '/register'
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>