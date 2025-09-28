// As constantes API_BASE_URL e BASE_URL são definidas no footer.php para serem usadas globalmente
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

        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (container.contains(notification)) container.removeChild(notification);
            }, 500);
        }, 3000);
    }

    // Atualiza o contador de itens no ícone do carrinho
    function updateCartCount() {
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        const totalItems = carrinho.reduce((sum, item) => sum + item.quantidade, 0);
        const cartCountDisplay = document.querySelector('.cart-count-display');
        if (cartCountDisplay) {
            cartCountDisplay.style.display = totalItems > 0 ? 'inline-block' : 'none';
            cartCountDisplay.textContent = totalItems;
        }
    }

    // Atualiza a interface (links de login/logout/admin)
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
            updateAuthUI(false, false);
        }
    }

    // Evento de clique para o botão de logout
    const navLogout = document.getElementById('nav-logout');
    if (navLogout) {
        navLogout.addEventListener('click', async (event) => {
            event.preventDefault();
            const response = await fetch(`${API_BASE_URL}/logout`, { method: 'POST' });
            const data = await response.json();
            if (data.success) {
                localStorage.removeItem('carrinho');
                showNotification(data.message);
                // ALTERAÇÃO: Redireciona para a home após o logout
                setTimeout(() => window.location.href = `${BASE_URL}/home`, 1500);
            }
        });
    }

    // Atualiza o ano no rodapé
    const anoAtualSpan = document.getElementById('anoAtual');
    if (anoAtualSpan) anoAtualSpan.textContent = new Date().getFullYear();

    // Chamadas iniciais
    checkAuthStatus();
    updateCartCount();

    // ===================================================================
    // LÓGICA ESPECÍFICA DE CADA PÁGINA
    // ===================================================================

    // --- PÁGINA DE LOGIN ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, senha })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showNotification(data.message);
                // ALTERAÇÃO: Redireciona para a home após o login
                setTimeout(() => window.location.href = `${BASE_URL}/home`, 1500);
            } else {
                showNotification(data.message || 'Erro.', 'error');
            }
        });
    }

    // --- PÁGINA DE CADASTRO ---
    const cadastroForm = document.getElementById('cadastroForm');
    if (cadastroForm) {
        cadastroForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const response = await fetch(`${API_BASE_URL}/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome, email, senha })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showNotification(data.message);
                // ALTERAÇÃO: Redireciona para a página de login após o cadastro
                setTimeout(() => window.location.href = `${BASE_URL}/auth/login`, 1500);
            } else {
                showNotification(data.message || 'Erro.', 'error');
            }
        });
    }

    // --- LÓGICA DO CARRINHO (reutilizada em várias páginas) ---
    function addToCart(productId, productName, productPrice, productStock, productImagePath) {
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        const existingItem = carrinho.find(item => item.id == productId);

        if (existingItem) {
            if (existingItem.quantidade < productStock) {
                existingItem.quantidade++;
                showNotification(`${productName} adicionado novamente.`);
            } else {
                showNotification(`Estoque insuficiente para ${productName}.`, 'error');
            }
        } else {
            if (productStock > 0) {
                carrinho.push({ id: productId, nome: productName, preco: productPrice, quantidade: 1, estoque_disponivel: productStock, imagem_path: productImagePath});
                showNotification(`${productName} adicionado ao carrinho.`);
            } else {
                showNotification(`${productName} está fora de estoque.`, 'error');
            }
        }
        localStorage.setItem('carrinho', JSON.stringify(carrinho));
        updateCartCount();
    }
    
    // --- PÁGINA DE PRODUTOS ---
    const produtosContainer = document.getElementById('produtos-container');
    if (produtosContainer) {
        async function fetchProducts() {
            try {
                // A requisição agora funciona para todos os usuários, não apenas admin
                const response = await fetch(`${API_BASE_URL}/produtos`);
                if (!response.ok) throw new Error('Falha ao carregar produtos.');
                
                const products = await response.json();
                produtosContainer.innerHTML = '';

                if (products.length > 0) {
                    products.forEach(product => {
                        const productDiv = document.createElement('div');
                        productDiv.classList.add('produto');
                        const imageUrl = product.imagem ? `${BASE_URL}/${product.imagem}` : `${BASE_URL}/images/produtos/placeholder.png`;
                        productDiv.innerHTML = `
                            <img src="${imageUrl}" alt="${product.nome}">
                            <dl>
                                <dt>${product.nome}</dt>
                                <dd>Preço: R$ ${parseFloat(product.preco).toFixed(2)}</dd>
                                <dd>Estoque: ${product.estoque}</dd>
                                <dd>
                                    <button class="add-to-cart" data-id="${product.id}" data-nome="${product.nome}" data-preco="${product.preco}" 
                                    data-estoque="${product.estoque}" data-imagem-path="${product.imagem}">
                                        <i class="fas fa-shopping-cart"></i> Adicionar ao carrinho
                                    </button>
                                </dd>
                            </dl>
                        `;
                        produtosContainer.appendChild(productDiv);
                    });
                    
                    document.querySelectorAll('.add-to-cart').forEach(button => {
                        button.addEventListener('click', function() {
                            addToCart(this.dataset.id, this.dataset.nome, parseFloat(this.dataset.preco), parseInt(this.dataset.estoque));
                        });
                    });
                } else {
                    produtosContainer.innerHTML = '<p>Nenhum produto disponível no momento.</p>';
                }
            } catch (error) {
                produtosContainer.innerHTML = '<p>Erro ao carregar produtos. Tente novamente mais tarde.</p>';
                console.error(error);
            }
        }
        fetchProducts();
    }
    
    // --- PÁGINA DO CARRINHO ---
    const carrinhoContainer = document.getElementById('carrinho-itens-container');
    if(carrinhoContainer) {
        const carrinhoResumoContainer = document.getElementById('carrinho-resumo-container');
        const carrinhoTotalSpan = document.getElementById('carrinho-total');
        const finalizarPedidoBtn = document.getElementById('finalizar-pedido');
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

        function renderCart() {
            carrinhoContainer.innerHTML = '';
            let total = 0;

            if (carrinho.length === 0) {
                carrinhoContainer.innerHTML = '<p class="carrinho-vazio">O seu carrinho está vazio.</p>';
                carrinhoResumoContainer.style.display = 'none';
            } else {
                carrinhoResumoContainer.style.display = 'block';
                carrinho.forEach(item => {
                    const itemTotal = item.preco * item.quantidade;
                    total += itemTotal;
                    const itemDiv = document.createElement('div');
                    itemDiv.classList.add('carrinho-item');
                    itemDiv.innerHTML = `
                        <img src="${imageUrl}" alt="${item.nome}" class="item-image">
                        <div class="item-details">
                            <span class="item-name">${item.nome}</span>
                            <span class="item-price">R$ ${parseFloat(item.preco).toFixed(2)}</span>
                        </div>
                        <div class="item-controls">
                            <input type="number" value="${item.quantidade}" min="1" max="${item.estoque_disponivel}" data-id="${item.id}" class="quantidade-input">
                            <span class="item-subtotal">R$ ${itemTotal.toFixed(2)}</span>
                            <button class="remover-item" data-id="${item.id}" title="Remover item"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    `;
                    carrinhoContainer.appendChild(itemDiv);
                });
            }
            carrinhoTotalSpan.textContent = total.toFixed(2);
            updateCartCount();
        }

        carrinhoContainer.addEventListener('change', (event) => {
            if (event.target.classList.contains('quantidade-input')) {
                const itemId = event.target.dataset.id;
                const newQuantity = parseInt(event.target.value);
                const item = carrinho.find(i => i.id == itemId);
                if (item) {
                    item.quantidade = newQuantity;
                    localStorage.setItem('carrinho', JSON.stringify(carrinho));
                    renderCart();
                }
            }
        });

        carrinhoContainer.addEventListener('click', (event) => {
            const removerBtn = event.target.closest('.remover-item');
            if (removerBtn) {
                const itemId = removerBtn.dataset.id;
                carrinho = carrinho.filter(i => i.id != itemId);
                localStorage.setItem('carrinho', JSON.stringify(carrinho));
                showNotification('Item removido do carrinho.');
                renderCart();
            }
        });

        finalizarPedidoBtn.addEventListener('click', async () => {
            if (carrinho.length === 0) {
                showNotification('O seu carrinho está vazio.', 'error');
                return;
            }
            const pedido = {
                carrinho: carrinho.map(item => ({ produto_id: item.id, quantidade: item.quantidade })),
                tipo_entrega: 'retirada'
            };
            const response = await fetch(`${API_BASE_URL}/pedidos`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(pedido)
            });
            const data = await response.json();
            if (response.ok) {
                showNotification(data.message);
                localStorage.removeItem('carrinho');
                carrinho = [];
                renderCart();
                // ALTERAÇÃO: Redireciona para a página da conta após finalizar o pedido
                setTimeout(() => window.location.href = `${BASE_URL}/conta`, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        });
        
        renderCart();
    }
    
    // --- PÁGINA MINHA CONTA ---
    const meusPedidosLista = document.getElementById('meus-pedidos-lista');
    if(meusPedidosLista) {
        async function loadUserOrders() {
            const response = await fetch(`${API_BASE_URL}/pedidos`);
            if (!response.ok) {
                showNotification('Precisa de estar logado para ver os seus pedidos.', 'error');
                setTimeout(() => window.location.href = `${BASE_URL}/auth/login`, 2000);
                return;
            }
            const orders = await response.json();
            meusPedidosLista.innerHTML = '';
            if (orders.length === 0) {
                meusPedidosLista.innerHTML = '<div class="nenhum-pedido"><h3>Você ainda não fez nenhum pedido.</h3></div>';
                return;
            }
            orders.forEach(order => {
                let itensHtml = '';
                // Verifica se 'itens' existe e é um array
                if (order.itens && Array.isArray(order.itens)) {
                    order.itens.forEach(item => {
                        itensHtml += `<div class="pedido-item-produto">
                            <img src="${API_BASE_URL}/produtoImagem/${item.produto_id}" alt="${item.produto_nome}" class="produto-imagem-pedido">
                            <div class="produto-detalhes">
                                <strong>${item.produto_nome}</strong>
                                <span>Quantidade: ${item.quantidade}</span>
                            </div>
                        </div>`;
                    });
                }
                const orderDiv = document.createElement('div');
                orderDiv.classList.add('pedido-item');
                orderDiv.innerHTML = `
                    <div class="pedido-header">
                        <div><strong>Pedido #${order.id}</strong> - <span>${new Date(order.data_pedido).toLocaleDateString('pt-BR')}</span></div>
                        <span class="status ${order.status}">${order.status}</span>
                        <span>Total: <strong>R$ ${parseFloat(order.total).toFixed(2)}</strong></span>
                    </div>
                    <div class="pedido-body">${itensHtml}</div>
                `;
                meusPedidosLista.appendChild(orderDiv);
            });
        }
        loadUserOrders();
    }
    
    // --- PÁGINA ADMIN PRODUTOS ---
    const adminProdutosContainer = document.getElementById('lista-produtos-admin');
    if(adminProdutosContainer) {
        const form = document.getElementById('addProdutoForm');
        const formTitle = document.getElementById('form-title');
        const submitBtn = document.getElementById('submit-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');

        async function loadProductsAdmin() {
            const response = await fetch(`${API_BASE_URL}/produtos`);
            const products = await response.json();
            adminProdutosContainer.innerHTML = '';
            products.forEach(product => {
                const productItem = document.createElement('div');
                const imageUrl = product.imagem ? `${BASE_URL}/${product.imagem}` : `${BASE_URL}/images/produtos/placeholder.png`;
                productItem.classList.add('produto-admin-item');
                productItem.innerHTML = `
                    <img src="${imageUrl}/produtoImagem/${product.id}" alt="${product.nome}" class="produto-admin-img">
                    <div class="produto-admin-info">
                        <strong>${product.nome}</strong>
                        <span>R$ ${parseFloat(product.preco).toFixed(2)} | Estoque: ${product.estoque}</span>
                    </div>
                    <div class="produto-admin-actions">
                        <button class="btn-edit-produto" data-id="${product.id}" data-nome="${product.nome}" data-preco="${product.preco}" data-estoque="${product.estoque}"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn-delete-produto" data-id="${product.id}" data-nome="${product.nome}"><i class="fas fa-trash"></i> Remover</button>
                    </div>
                `;
                adminProdutosContainer.appendChild(productItem);
            });
        }

        function resetForm() {
            form.reset();
            document.getElementById('edit-id').value = '';
            formTitle.textContent = 'Adicionar Novo Produto';
            submitBtn.textContent = 'Adicionar Produto';
            cancelEditBtn.style.display = 'none';
        }

        adminProdutosContainer.addEventListener('click', (event) => {
            if (event.target.closest('.btn-edit-produto')) {
                const btn = event.target.closest('.btn-edit-produto');
                document.getElementById('edit-id').value = btn.dataset.id;
                document.getElementById('nome-produto').value = btn.dataset.nome;
                document.getElementById('preco-produto').value = btn.dataset.preco;
                document.getElementById('estoque-produto').value = btn.dataset.estoque;
                formTitle.textContent = 'Editar Produto';
                submitBtn.textContent = 'Atualizar Produto';
                cancelEditBtn.style.display = 'inline-block';
                window.scrollTo(0, 0);
            }
            if (event.target.closest('.btn-delete-produto')) {
                // Lógica de exclusão
            }
        });

        cancelEditBtn.addEventListener('click', resetForm);

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(form);
            const editId = document.getElementById('edit-id').value;
            let response;

            if (editId) {
                formData.append('_method', 'PUT');
                response = await fetch(`${API_BASE_URL}/produtos`, { method: 'POST', body: formData });
            } else {
                response = await fetch(`${API_BASE_URL}/produtos`, { method: 'POST', body: formData });
            }
            
            const data = await response.json();
            if(response.ok) {
                showNotification(data.message);
                resetForm();
                loadProductsAdmin();
            } else {
                showNotification(data.message, 'error');
            }
        });

        loadProductsAdmin();
    }
    
    // --- PÁGINA ADMIN FATURAMENTO ---
    const adminFaturamentoContainer = document.getElementById('faturamento-data');
    if(adminFaturamentoContainer) {
        async function loadFaturamento() {
            const response = await fetch(`${API_BASE_URL}/pedidos?faturamento=true`);
            const data = await response.json();
            const container = document.getElementById('faturamento-data');
            container.innerHTML = '<table><thead><tr><th>Data</th><th>Faturamento Diário</th></tr></thead><tbody></tbody></table>';
            const tbody = container.querySelector('tbody');
            data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${item.data}</td><td>R$ ${parseFloat(item.faturamento_diario).toFixed(2)}</td>`;
                tbody.appendChild(tr);
            });
        }

        async function loadAllOrdersAdmin() {
            const response = await fetch(`${API_BASE_URL}/pedidos`);
            const orders = await response.json();
            const container = document.getElementById('lista-pedidos-admin');
            container.innerHTML = '<table><thead><tr><th>Pedido ID</th><th>Cliente</th><th>Data</th><th>Total</th><th>Status</th></tr></thead><tbody></tbody></table>';
            const tbody = container.querySelector('tbody');
            orders.forEach(order => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>#${order.id}</td>
                    <td>${order.cliente_nome}</td>
                    <td>${new Date(order.data_pedido).toLocaleString('pt-BR')}</td>
                    <td>R$ ${parseFloat(order.total).toFixed(2)}</td>
                    <td>
                        <select class="status-pedido-select" data-id="${order.id}">
                            <option value="pendente" ${order.status === 'pendente' ? 'selected' : ''}>Pendente</option>
                            <option value="processando" ${order.status === 'processando' ? 'selected' : ''}>Processando</option>
                            <option value="concluido" ${order.status === 'concluido' ? 'selected' : ''}>Concluído</option>
                            <option value="cancelado" ${order.status === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                        </select>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.querySelectorAll('.status-pedido-select').forEach(select => {
                select.addEventListener('change', async function() {
                    const response = await fetch(`${API_BASE_URL}/pedidos`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ pedido_id: this.dataset.id, status: this.value })
                    });
                    const data = await response.json();
                    if(response.ok) showNotification(data.message);
                    else showNotification(data.message, 'error');
                });
            });
        }

        loadFaturamento();
        loadAllOrdersAdmin();
    }
});