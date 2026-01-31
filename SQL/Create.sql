-- =============================================================================

-- PROGETTO EASYDRIVE

-- =============================================================================



CREATE DATABASE IF NOT EXISTS `easydrive_db`

DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;



USE `easydrive_db`;



-- -----------------------------------------------------

-- Tabella 1: Account

-- -----------------------------------------------------

CREATE TABLE `Account` (

    `username` VARCHAR(50) NOT NULL,

    `hashedPassword` VARCHAR(255) NOT NULL,

    `nome` VARCHAR(100) NOT NULL,

    `cognome` VARCHAR(100) NOT NULL,

    `sesso` ENUM('M', 'F', 'Altro') NOT NULL,

    `email` VARCHAR(100) NOT NULL,

    `numeroTelefono` VARCHAR(15) NOT NULL,

    `dataNascita` DATE NOT NULL,

    `nazione` VARCHAR(50) NOT NULL DEFAULT 'Italia',

    `provincia` VARCHAR(50) NOT NULL,

    `citta` VARCHAR(50) NOT NULL,

    `via` VARCHAR(100) NOT NULL,

    `numeroCivico` VARCHAR(10) NOT NULL,

    `cap` VARCHAR(5) NOT NULL,

    `ruolo` ENUM(

        'cliente_non_registrato',

        'cliente_registrato',

        'gestore_veicoli',

        'gestore_vendite',

        'amministratore'

    ) NOT NULL DEFAULT 'cliente_registrato',

    `dataCreazione` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    `AccountId` INT AUTO_INCREMENT,

    PRIMARY KEY (`username`),

    UNIQUE (`email`),

    UNIQUE (`AccountId`)

) ENGINE=InnoDB;



-- -----------------------------------------------------

-- Tabella 2: Veicolo

-- -----------------------------------------------------

CREATE TABLE `Veicolo` (

    `telaio` VARCHAR(17) NOT NULL,

    `targa` VARCHAR(10),

    `marca` VARCHAR(50) NOT NULL,

    `modello` VARCHAR(50) NOT NULL,

    `annoImmatricolazione` INT NOT NULL,

    `chilometraggio` INT NOT NULL DEFAULT 0,

    `tipoAlimentazione` ENUM('Benzina','Diesel','Elettrica','Ibrida','GPL','Metano') NOT NULL,

    `tipoCambio` ENUM('Manuale','Automatico') NOT NULL,

    `categoria` ENUM('Auto','SUV','Furgone','Hatchback','Sedan') NOT NULL,

    `prezzoVendita` DECIMAL(12,2),

    `tariffaNoleggioGiorno` DECIMAL(10,2),

    `numeroPosti` INT DEFAULT 5,

    `descrizione` TEXT,

    `stato` ENUM(

        'InRevisione',

        'Disponibile',

        'InPrenotazione',

        'Noleggiato',

        'InManutenzione',

        'Ritirato',

        'Venduto'

    ) DEFAULT 'InRevisione',

    PRIMARY KEY (`telaio`),

    UNIQUE (`targa`)

) ENGINE=InnoDB;



-- -----------------------------------------------------

-- Tabella 3: Immagine_Veicolo

-- -----------------------------------------------------

CREATE TABLE `Immagine_Veicolo` (

    `id_immagine` INT AUTO_INCREMENT,

    `url_immagine` VARCHAR(400) NOT NULL,

    `is_principale` TINYINT(1) DEFAULT 0,

    `telaio_veicolo` VARCHAR(17) NOT NULL,

    PRIMARY KEY (`id_immagine`),

    FOREIGN KEY (`telaio_veicolo`) REFERENCES `Veicolo`(`telaio`)

        ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB;



-- -----------------------------------------------------

-- Tabella 4: Prenotazione_Noleggio

-- -----------------------------------------------------

CREATE TABLE `Prenotazione_Noleggio` (

    `id_prenotazione` INT AUTO_INCREMENT,

    `data_inizio` DATETIME NOT NULL,

    `data_fine` DATETIME NOT NULL,

    `luogo_ritiro` VARCHAR(100) NOT NULL,

    `luogo_consegna` VARCHAR(100) NOT NULL,

    `prezzo_stimato` DECIMAL(12,2) NOT NULL,

    `stato_prenotazione` ENUM(

        'InAttesa',

        'Confermata',

        'InCorso',

        'Conclusa',

        'Annullata'

    ) DEFAULT 'InAttesa',

    `username` VARCHAR(50) NOT NULL,

    `telaio` VARCHAR(17) NOT NULL,

    `data_creazione` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_prenotazione`),

    FOREIGN KEY (`username`) REFERENCES `Account`(`username`),

    FOREIGN KEY (`telaio`) REFERENCES `Veicolo`(`telaio`)

) ENGINE=InnoDB;

-- -----------------------------------------------------

-- Tabella 5: Ordine_Vendita

-- -----------------------------------------------------

CREATE TABLE `Ordine_Vendita` (

    `id_ordine` INT AUTO_INCREMENT,

    `codice_univoco` VARCHAR(20) NOT NULL,

    `prezzo_finale` DECIMAL(12,2) NOT NULL,

    `data_ordine` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    `metodo_pagamento` VARCHAR(50),

    `stato_pagamento` ENUM('Pendente','Completato','Fallito') DEFAULT 'Pendente',

    `username` VARCHAR(50) NOT NULL,

    `telaio` VARCHAR(17) NOT NULL,

    PRIMARY KEY (`id_ordine`),

    UNIQUE (`codice_univoco`),

    FOREIGN KEY (`username`) REFERENCES `Account`(`username`),

    FOREIGN KEY (`telaio`) REFERENCES `Veicolo`(`telaio`)

) ENGINE=InnoDB;



-- -----------------------------------------------------

-- Tabella 6: Wishlist_Interesse

-- -----------------------------------------------------

CREATE TABLE `Wishlist_Interesse` (

    `id_interesse` INT AUTO_INCREMENT,

    `username` VARCHAR(50) NOT NULL,

    `telaio` VARCHAR(17) NOT NULL,

    `data_aggiunta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_interesse`),

    FOREIGN KEY (`username`) REFERENCES `Account`(`username`) ON DELETE CASCADE,

    FOREIGN KEY (`telaio`) REFERENCES `Veicolo`(`telaio`) ON DELETE CASCADE

) ENGINE=InnoDB;



-- -----------------------------------------------------

-- Tabella 7: Log_Disponibilita

-- -----------------------------------------------------

CREATE TABLE `Log_Disponibilita` (

    `id_log` INT AUTO_INCREMENT,

    `telaio` VARCHAR(17) NOT NULL,

    `stato_precedente` VARCHAR(50),

    `stato_nuovo` VARCHAR(50),

    `data_cambio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    `motivo` VARCHAR(255),

    PRIMARY KEY (`id_log`)

) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabella 8: icone
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS icone (
    id_icona INT AUTO_INCREMENT PRIMARY KEY,
    nome_icona VARCHAR(50) NOT NULL UNIQUE,
    url_icona TEXT NOT NULL,
    descrizione VARCHAR(100)
) ENGINE=InnoDB;


USE `easydrive_db`;

-- Vista Catalogo
CREATE OR REPLACE VIEW `VistaCatalogo` AS
SELECT 
    v.`marca`, v.`modello`, v.`annoImmatricolazione`, v.`prezzoVendita`, 
    v.`tariffaNoleggioGiorno`, v.`stato`, i.`url_immagine`
FROM `Veicolo` v
LEFT JOIN `Immagine_Veicolo` i ON v.`telaio` = i.`telaio_veicolo` AND i.`is_principale` = 1
WHERE v.`stato` = 'Disponibile';

-- Vista Storico
CREATE OR REPLACE VIEW `StoricoOperazioni` AS
SELECT 'Vendita' AS `Tipo`, ov.`data_ordine` AS `Data`, ov.`prezzo_finale` AS `Importo`, ov.`username` AS `Cliente`
FROM `Ordine_Vendita` ov
UNION ALL
SELECT 'Noleggio' AS `Tipo`, pn.`data_creazione` AS `Data`, pn.`prezzo_stimato` AS `Importo`, pn.`username` AS `Cliente`
FROM `Prenotazione_Noleggio` pn;
