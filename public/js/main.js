// A variável API_BASE_URL é definida no footer.php para ser usada globalmente
document.addEventListener('DOMContentLoaded', function() {
    
    // ===================================================================
    // FUNÇÕES GLOBAIS E INICIALIZAÇÃO
    // ===================================================================

    // Função universal para exibir notificações
    window.showNotification = function(message, type = 'success') {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (container.contains(notification)) {
                    container.removeChild(notification);
                }
            }, 500);
        }, 3000);
    }

    // Atualiza o contador de itens no ícone do carrinho
    function updateCartCount() {
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        const totalItems = carrinho.reduce((sum, item) => sum + item.quantidade, 0);
        const cartCountDisplay = document.querySelector('.cart-count-display');
        if (cartCountDisplay) {
            if (totalItems > 0) {
                cartCountDisplay.textContent = totalItems;
                cartCountDisplay.style.display = 'inline-block';
            } else {
                cartCountDisplay.style.display = 'none';
            }
        }
    }

    // Atualiza a interface (links de login/logout/admin) com base no status de autenticação
    function updateAuthUI(isAuthenticated, isAdmin) {
        const navLogin = document.getElementById('nav-login');
        const navCadastro = document.getElementById('nav-cadastro');
        const navConta = document.getElementById('nav-conta');
        const navAdmin = document.getElementById('nav-admin');
        const navLogout = document.getElementById('nav-logout');

        if (isAuthenticated) {
            if (navLogin) navLogin.style.display = 'none';
            if (navCadastro) navCadastro.style.display = 'none';
            if (navConta) navConta.style.display = 'inline-block';
            if (navLogout) navLogout.style.display = 'inline-block';
            if (navAdmin) navAdmin.style.display = isAdmin ? 'inline-block' : 'none';
        } else {
            if (navLogin) navLogin.style.display = 'inline-block';
            if (navCadastro) navCadastro.style.display = 'inline-block';
            if (navConta) navConta.style.display = 'none';
            if (navAdmin) navAdmin.style.display = 'none';
            if (navLogout) navLogout.style.display = 'none';
        }
    }

    // Verifica o status de autenticação com a API
    async function checkAuthStatus() {
        try {
            const response = await fetch(`${API_BASE_URL}/authStatus`);
            const data = await response.json();
            updateAuthUI(data.isAuthenticated, data.isAdmin);
        } catch (error) {
            console.error('Erro ao verificar status de autenticação:', error);
            updateAuthUI(false, false); // Assume deslogado em caso de erro de rede
        }
    }

    // Evento de clique para o botão de logout
    const navLogout = document.getElementById('nav-logout');
    if (navLogout) {
        navLogout.addEventListener('click', async function(event) {
            event.preventDefault();
            try {
                const response = await fetch(`${API_BASE_URL}/logout`, { method: 'POST' });
                const data = await response.json();
                if (data.success) {
                    localStorage.removeItem('carrinho');
                    showNotification(data.message);
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}/home`;
                    }, 1500);
                }
            } catch (error) {
                showNotification('Ocorreu um erro ao tentar sair.', 'error');
            }
        });
    }

    // Atualiza o ano no rodapé
    const anoAtualSpan = document.getElementById('anoAtual');
    if (anoAtualSpan) {
        anoAtualSpan.textContent = new Date().getFullYear();
    }

    // Chamadas iniciais
    checkAuthStatus();
    updateCartCount();

    // ===================================================================
    // LÓGICA ESPECÍFICA DE CADA PÁGINA
    // ===================================================================

    // --- PÁGINA DE LOGIN ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            
            try {
                const response = await fetch(`${API_BASE_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, senha })
                });
                const data = await response.json();
                
                if (response.ok && data.success) {
                    showNotification(data.message);
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}/home`;
                    }, 1500);
                } else {
                    showNotification(data.message || 'Erro desconhecido.', 'error');
                }
            } catch (error) {
                showNotification('Ocorreu um erro ao tentar fazer login.', 'error');
            }
        });
    }

    // --- PÁGINA DE CADASTRO ---
    const cadastroForm = document.getElementById('cadastroForm');
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;

            try {
                const response = await fetch(`${API_BASE_URL}/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome, email, senha })
                });
                const data = await response.json();
                
                if (response.ok && data.success) {
                    showNotification(data.message);
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}/auth/login`;
                    }, 1500);
                } else {
                    showNotification(data.message || 'Erro ao cadastrar.', 'error');
                }
            } catch (error) {
                showNotification('Ocorreu um erro ao tentar cadastrar.', 'error');
            }
        });
    }

    // --- PÁGINA DE PRODUTOS ---
    const produtosContainer = document.getElementById('produtos-container');
    if (produtosContainer) {
        async function fetchProducts() {
            // Lógica para carregar produtos da API e renderizar na página
        }
        fetchProducts();
    }
    
    // --- PÁGINA DO CARRINHO ---
    const carrinhoContainer = document.getElementById('carrinho-itens-container');
    if(carrinhoContainer) {
        // Lógica para renderizar o carrinho e finalizar o pedido
    }
    
    // --- PÁGINA MINHA CONTA ---
    const meusPedidosLista = document.getElementById('meus-pedidos-lista');
    if(meusPedidosLista) {
        // Lógica para carregar os pedidos do usuário
    }
    
    // --- PÁGINAS DE ADMIN ---
    if(document.body.classList.contains('admin-page')) { // Adicione a classe 'admin-page' no <body> das páginas de admin
        // Lógica comum a todas as páginas de admin, como verificação de permissão
    }
    
    const adminProdutosContainer = document.getElementById('lista-produtos-admin');
    if(adminProdutosContainer) {
        // Lógica para CRUD de produtos
    }
    
    const adminFaturamentoContainer = document.getElementById('faturamento-data');
    if(adminFaturamentoContainer) {
        // Lógica para carregar faturamento e gerenciar pedidos
    }

});