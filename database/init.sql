-- Criar banco de dados se não existir
CREATE DATABASE IF NOT EXISTS db_usuario;
USE db_usuario;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar índices
CREATE INDEX idx_email ON usuarios(email);
CREATE INDEX idx_nome ON usuarios(nome);

-- Garantir privilégios para o usuário
GRANT ALL PRIVILEGES ON db_usuario.* TO 'admin'@'%';
FLUSH PRIVILEGES;
