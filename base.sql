-- base.sql
-- Structure de la base de données pour la simulation Mobile Money

PRAGMA foreign_keys = ON;

-- 1. Table des opérateurs
CREATE TABLE IF NOT EXISTS operators (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    operator_code VARCHAR(50) UNIQUE, -- Code unique pour l'opérateur (v2)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table des préfixes téléphoniques
CREATE TABLE IF NOT EXISTS phone_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefix VARCHAR(10) NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2b. Table des préfixes par opérateur (v2) - Chaque opérateur peut gérer plusieurs préfixes
CREATE TABLE IF NOT EXISTS operator_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operator_id INTEGER NOT NULL,
    prefix VARCHAR(10) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operator_id) REFERENCES operators(id) ON DELETE CASCADE,
    UNIQUE (operator_id, prefix)
);

-- 3. Table des types d'opérations
CREATE TABLE IF NOT EXISTS operation_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3b. Table des commissions inter-opérateurs (v2) - Commission % pour les transferts vers d'autres opérateurs
CREATE TABLE IF NOT EXISTS operator_commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operator_id INTEGER NOT NULL,
    operation_type_id INTEGER NOT NULL,
    commission_percentage DECIMAL(5, 2) NOT NULL DEFAULT 0.00, -- Pourcentage de commission
    min_amount DECIMAL(15, 2),
    max_amount DECIMAL(15, 2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operator_id) REFERENCES operators(id) ON DELETE CASCADE,
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id) ON DELETE CASCADE,
    CHECK (commission_percentage >= 0),
    CHECK (min_amount IS NULL OR max_amount IS NULL OR min_amount <= max_amount)
);

-- 4. Table des tranches de frais
CREATE TABLE IF NOT EXISTS fee_brackets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operation_type_id INTEGER NOT NULL,
    min_amount DECIMAL(15, 2) NOT NULL,
    max_amount DECIMAL(15, 2) NOT NULL,
    fee_amount DECIMAL(15, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id) ON DELETE CASCADE,
    CHECK (min_amount <= max_amount),
    CHECK (fee_amount >= 0)
);

-- 5. Table des clients
CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    phone_number VARCHAR(20) NOT NULL UNIQUE,
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) NOT NULL DEFAULT 'active', -- 'active', 'suspended'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (balance >= 0)
);

-- 6. Table des transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_reference VARCHAR(50) NOT NULL UNIQUE,
    operation_type_id INTEGER NOT NULL,
    sender_client_id INTEGER,
    receiver_client_id INTEGER,
    sender_operator_id INTEGER, -- v2: Opérateur source
    receiver_operator_id INTEGER, -- v2: Opérateur destinataire
    amount DECIMAL(15, 2) NOT NULL,
    fee_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    inter_operator_commission DECIMAL(15, 2) NOT NULL DEFAULT 0.00, -- v2: Commission inter-opérateurs
    total_amount DECIMAL(15, 2) NOT NULL,
    balance_before DECIMAL(15, 2) NOT NULL,
    balance_after DECIMAL(15, 2) NOT NULL,
    include_withdrawal_fee BOOLEAN DEFAULT 0, -- v2: Si les frais de retrait sont inclus
    status VARCHAR(20) NOT NULL DEFAULT 'completed', -- 'pending', 'completed', 'failed', 'cancelled'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operation_type_id) REFERENCES operation_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (sender_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (sender_operator_id) REFERENCES operators(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_operator_id) REFERENCES operators(id) ON DELETE SET NULL,
    CHECK (amount > 0),
    CHECK (fee_amount >= 0),
    CHECK (inter_operator_commission >= 0)
);

-- Index pour optimiser les recherches
CREATE INDEX IF NOT EXISTS idx_phone_prefixes_prefix ON phone_prefixes(prefix);
CREATE INDEX IF NOT EXISTS idx_clients_phone_number ON clients(phone_number);
CREATE INDEX IF NOT EXISTS idx_transactions_reference ON transactions(transaction_reference);
CREATE INDEX IF NOT EXISTS idx_transactions_created_at ON transactions(created_at);
CREATE INDEX IF NOT EXISTS idx_transactions_sender ON transactions(sender_client_id);
CREATE INDEX IF NOT EXISTS idx_transactions_receiver ON transactions(receiver_client_id);
CREATE INDEX IF NOT EXISTS idx_fee_brackets_operation ON fee_brackets(operation_type_id);

-- 7. Table des destinataires multiples de transactions (v2) - Pour les envois vers plusieurs numéros
CREATE TABLE IF NOT EXISTS transaction_recipients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    receiver_phone_number VARCHAR(20) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    CHECK (amount > 0)
);

-- Index pour transaction_recipients
CREATE INDEX IF NOT EXISTS idx_transaction_recipients_transaction ON transaction_recipients(transaction_id);

-- ==========================================
-- INSERTION DES DONNÉES INITIALES
-- ==========================================

-- Opérateur de démo (admin / admin123)
-- Le hash de "admin123" généré par password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO operators (username, password_hash, operator_code) VALUES 
('admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'OP_ADMIN');

-- Préfixes téléphoniques par défaut
INSERT INTO phone_prefixes (prefix, is_active) VALUES 
('032', 1),
('033', 1),
('034', 1),
('037', 1),
('038', 1);

-- v2: Configuration des préfixes pour l'opérateur admin
INSERT INTO operator_prefixes (operator_id, prefix, is_active) VALUES 
(1, '032', 1),
(1, '033', 1),
(1, '034', 1),
(1, '037', 1),
(1, '038', 1);

-- Types d'opérations
INSERT INTO operation_types (id, code, name, is_active) VALUES 
(1, 'DEPOSIT', 'Dépôt', 1),
(2, 'WITHDRAWAL', 'Retrait', 1),
(3, 'TRANSFER', 'Transfert', 1);

-- v2: Commissions inter-opérateurs pour l'opérateur admin (2% sur les transferts vers autres opérateurs)
INSERT INTO operator_commissions (operator_id, operation_type_id, commission_percentage) VALUES 
(1, 3, 2.00); -- 2% de commission sur les transferts

-- Barèmes de frais pour le Dépôt (souvent 0 ou fixe dans la réalité, ici un exemple)
INSERT INTO fee_brackets (operation_type_id, min_amount, max_amount, fee_amount) VALUES 
(1, 0, 999999999, 0); -- Les dépôts sont gratuits

-- Barèmes de frais pour le Retrait (exemple)
INSERT INTO fee_brackets (operation_type_id, min_amount, max_amount, fee_amount) VALUES 
(2, 100, 1000, 50),
(2, 1001, 5000, 100),
(2, 5001, 10000, 200),
(2, 10001, 25000, 500),
(2, 25001, 50000, 1000),
(2, 50001, 100000, 2000),
(2, 100001, 250000, 4000),
(2, 250001, 500000, 8000),
(2, 500001, 1000000, 12000),
(2, 1000001, 999999999, 15000);

-- Barèmes de frais pour le Transfert (exemple de l'énoncé)
INSERT INTO fee_brackets (operation_type_id, min_amount, max_amount, fee_amount) VALUES 
(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 10001, 25000, 200),
(3, 25001, 50000, 400),
(3, 50001, 100000, 800),
(3, 100001, 250000, 1500),
(3, 250001, 500000, 1500),
(3, 500001, 1000000, 2500),
(3, 1000001, 2000000, 3000),
(3, 2000001, 999999999, 5000);

-- Clients de démo
INSERT INTO clients (phone_number, balance) VALUES 
('0340000001', 50000.00),
('0320000002', 150000.00),
('0330000003', 0.00);
