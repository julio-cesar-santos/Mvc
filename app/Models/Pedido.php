<?php
class Pedido {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY data_pedido DESC");
        $stmt->execute([$userId]);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Anexa os itens a cada pedido
        foreach ($pedidos as &$pedido) {
            $stmt_itens = $this->db->prepare("SELECT ip.*, p.nome as produto_nome FROM itens_pedido ip JOIN produtos p ON ip.produto_id = p.id WHERE ip.pedido_id = ?");
            $stmt_itens->execute([$pedido['id']]);
            $pedido['itens'] = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pedidos;
    }

    public function getAllWithDetails() {
        $stmt = $this->db->prepare("SELECT p.*, u.nome as cliente_nome FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.data_pedido DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getFaturamento() {
        $stmt = $this->db->query("SELECT DATE(data_pedido) as data, SUM(total) as faturamento_diario FROM pedidos WHERE status = 'concluido' GROUP BY DATE(data_pedido) ORDER BY data DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($userId, $carrinho, $tipoEntrega) {
        $this->db->beginTransaction();
        try {
            $total_pedido = 0;
            $itens_para_inserir = [];

            $stmt_produto = $this->db->prepare("SELECT preco, estoque FROM produtos WHERE id = ? FOR UPDATE");
            $stmt_update_estoque = $this->db->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");

            foreach ($carrinho as $item) {
                $produto_id = $item['produto_id'];
                $quantidade = $item['quantidade'];

                $stmt_produto->execute([$produto_id]);
                $produto = $stmt_produto->fetch();

                if (!$produto || $produto['estoque'] < $quantidade) {
                    throw new Exception('Estoque insuficiente para o produto ID ' . $produto_id);
                }

                $total_item = $produto['preco'] * $quantidade;
                $total_pedido += $total_item;
                $itens_para_inserir[] = ['produto_id' => $produto_id, 'quantidade' => $quantidade, 'preco_unitario' => $produto['preco']];

                $stmt_update_estoque->execute([$quantidade, $produto_id]);
            }

            $stmt = $this->db->prepare("INSERT INTO pedidos (usuario_id, tipo_entrega, total) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $tipoEntrega, $total_pedido]);
            $pedido_id = $this->db->lastInsertId();

            $stmt_itens = $this->db->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            foreach ($itens_para_inserir as $item) {
                $stmt_itens->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $item['preco_unitario']]);
            }

            $this->db->commit();
            return $pedido_id;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Lança a exceção para ser tratada no Controller
            throw $e;
        }
    }
    
    public function updateStatus($pedidoId, $status) {
        // Lógica para devolver stock se o pedido for cancelado
        if ($status === 'cancelado') {
             $this->db->beginTransaction();
             try {
                $stmt_itens = $this->db->prepare("SELECT produto_id, quantidade FROM itens_pedido WHERE pedido_id = ?");
                $stmt_itens->execute([$pedidoId]);
                $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);

                foreach ($itens as $item) {
                    $stmt_restore = $this->db->prepare("UPDATE produtos SET estoque = estoque + ? WHERE id = ?");
                    $stmt_restore->execute([$item['quantidade'], $item['produto_id']]);
                }
                
                $stmt = $this->db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
                $stmt->execute([$status, $pedidoId]);
                $this->db->commit();
                return true;

             } catch(Exception $e) {
                $this->db->rollBack();
                throw $e;
             }
        } else {
            $stmt = $this->db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $pedidoId]);
        }
    }
}