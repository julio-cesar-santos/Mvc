<?php
class Produto {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT id, nome, preco, estoque, imagem FROM produtos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getImageById($id) {
        $stmt = $this->db->prepare("SELECT imagem FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public function create($nome, $preco, $estoque, $imagem_path = null) {
        $stmt = $this->db->prepare("INSERT INTO produtos (nome, preco, estoque, imagem) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $nome);
        $stmt->bindParam(2, $preco);
        $stmt->bindParam(3, $estoque);
        $stmt->bindParam(4, $imagem_path);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function update($id, $nome, $preco, $estoque, $imagem_path = null) {
        if ($imagem_path !== null) {
            $stmt = $this->db->prepare("UPDATE produtos SET nome = ?, preco = ?, estoque = ?, imagem = ? WHERE id = ?");
            $stmt->bindParam(1, $nome);
            $stmt->bindParam(2, $preco);
            $stmt->bindParam(3, $estoque);
            $stmt->bindParam(4, $imagem_path);
            $stmt->bindParam(5, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE produtos SET nome = ?, preco = ?, estoque = ? WHERE id = ?");
            $stmt->bindParam(1, $nome);
            $stmt->bindParam(2, $preco);
            $stmt->bindParam(3, $estoque);
            $stmt->bindParam(4, $id);
        }
        return $stmt->execute();
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM produtos WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}