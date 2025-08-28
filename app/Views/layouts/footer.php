<footer id="contato">
        <div class="container">
             <div class="contatos-footer">
                <h3>Contatos</h3>
                <p>
                    <a href="https://wa.me/5587991657703" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        (87) 99165-7703
                    </a>
                </p>
                <p>
                    <a href="https://instagram.com/laticinio_esperanca232" target="_blank"> <i class="fab fa-instagram"></i>
                        @laticinio_esperanca232
                    </a>
                </p>
            </div>
            <div class="links-footer">
                <h3>Navegação</h3>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/home">Início</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/produto">Produtos</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/auth/login">Login</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <span id="anoAtual"></span> Laticínio Esperança. Todos os direitos reservados.</p>
            <p>Projeto de Faculdade - Desenvolvimento Web</p>
        </div>
    </footer>
    <script>
        // Define a URL base para o JavaScript usar
        const API_BASE_URL = '<?php echo BASE_URL; ?>/api';
        const BASE_URL = '<?php echo BASE_URL; ?>'; // Adicionado para correção do bug
    </script>
    <script src="<?php echo BASE_URL; ?>/js/main.js"></script>
</body>
</html>