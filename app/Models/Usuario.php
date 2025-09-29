<?php
class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($nome, $email, $senha) {
        $hashed_password = password_hash($senha, PASSWORD_BCRYPT);
        try {
            $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            return $stmt->execute([$nome, $email, $hashed_password]);

        } catch (\PDOException $e) { 
            return false;
        }
    }

    public function login($email, $senha) {
        $user = this->findByEmail($email);
        if ($user && password_verify($senha, $user['senha'])) {
            return $user;
        }
        return false;
    }
}