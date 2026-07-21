-- Activer les clés étrangères SQLite
PRAGMA foreign_keys = ON;


-- Table des clients
CREATE TABLE users(
    id_user INTEGER PRIMARY KEY AUTOINCREMENT,
    numero VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(50) NOT NULL,
    solde DECIMAL(10,2) NOT NULL DEFAULT 0,
    pin_transaction VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Table des préfixes valides
CREATE TABLE prefix(
    id_prefix INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);


-- Table des types de transactions
CREATE TABLE type_transactions(
    id_type INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);


-- Table des frais par tranche
CREATE TABLE frais (
    id_frais INTEGER PRIMARY KEY AUTOINCREMENT,

    id_type INTEGER NOT NULL,

    montant_min DECIMAL(10,2) NOT NULL,
    montant_max DECIMAL(10,2) NOT NULL,

    frais DECIMAL(10,2) NOT NULL,

    FOREIGN KEY(id_type) 
        REFERENCES type_transactions(id_type)
);


-- Table des transactions
CREATE TABLE transactions(
    id_operation INTEGER PRIMARY KEY AUTOINCREMENT,

    id_client_source INTEGER,

    id_client_destination INTEGER,

    id_type INTEGER NOT NULL,

    montant DECIMAL(10,2) NOT NULL,

    frais DECIMAL(10,2) DEFAULT 0,

    date_operation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,


    FOREIGN KEY(id_client_source)
        REFERENCES users(id_user),

    FOREIGN KEY(id_client_destination)
        REFERENCES users(id_user),

    FOREIGN KEY(id_type)
        REFERENCES type_transactions(id_type)
);


-- Compte de l'opérateur pour les gains
CREATE TABLE compte_operateur (

    id_compte INTEGER PRIMARY KEY AUTOINCREMENT,

    total_gain DECIMAL(10,2) DEFAULT 0

);

INSERT INTO prefix(nom)
VALUES
('033'),
('037');

INSERT INTO type_transactions(nom)
VALUES
('Depot'),
('Retrait'),
('Transfert');

INSERT INTO compte_operateur(total_gain)
VALUES
(0);

INSERT INTO users(numero, nom, solde, pin_transaction)
VALUES
('0331234567','Michael',50000,'1234');

