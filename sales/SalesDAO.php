<?php
namespace it\unisa\easydrive\sales;
use it\unisa\easydrive\core\Database;

class SalesDAO {
    // Verifica disponibilità veicolo
    public function checkDisponibilita($telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT stato FROM Veicolo WHERE telaio = ? AND stato = 'Disponibile'");
        $stmt->bind_param("s", $telaio);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }


     public function getHistoryByUsername($username) {
        $db = Database::getConnection();
        $sql = "SELECT * FROM StoricoOperazioni WHERE Cliente = ? ORDER BY Data DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Verifica se l'interesse esiste già nel DB
    public function existsInteresse($username, $telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id_interesse FROM Wishlist_Interesse WHERE username = ? AND telaio = ?");
        $stmt->bind_param("ss", $username, $telaio);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Salva l'interesse nel DB
    public function insertInteresse($username, $telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO Wishlist_Interesse (username, telaio) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $telaio);
        return $stmt->execute();
    }
    public function getVehicleStatus($telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT stato FROM Veicolo WHERE telaio = ?");
        $stmt->bind_param("s", $telaio);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['stato'] : null;
    }

    public function removeFromWishlist($username, $telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM Wishlist_Interesse WHERE username = ? AND telaio = ?");
        $stmt->bind_param("ss", $username, $telaio);
        return $stmt->execute();
    }

    public function getVehiclesDetails($telaiArray) {
        if (empty($telaiArray)) return [];
        $db = Database::getConnection();
        
        // Creazione sicura della lista per la clausola IN
        $placeholders = implode(',', array_fill(0, count($telaiArray), '?'));
        $sql = "SELECT v.*, i.url_immagine 
                FROM Veicolo v 
                LEFT JOIN Immagine_Veicolo i ON v.telaio = i.telaio_veicolo 
                WHERE v.telaio IN ($placeholders) AND (i.is_principale = 1 OR i.is_principale IS NULL)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($telaiArray)), ...$telaiArray);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getCartTotal($telaiArray) {
        if (empty($telaiArray)) return 0;
        $db = Database::getConnection();
        
        $placeholders = implode(',', array_fill(0, count($telaiArray), '?'));
        $sql = "SELECT SUM(prezzoVendita) as totale FROM Veicolo WHERE telaio IN ($placeholders)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($telaiArray)), ...$telaiArray);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res['totale'] ?? 0;
    }
    public function getVehiclePrice($telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT prezzoVendita FROM Veicolo WHERE telaio = ?");
        $stmt->bind_param("s", $telaio);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['prezzoVendita'] : 0;
    }

    public function createOrderRecord($codice, $prezzo, $metodo, $username, $telaio) {
        $db = Database::getConnection();
        $sql = "INSERT INTO Ordine_Vendita (codice_univoco, prezzo_finale, metodo_pagamento, stato_pagamento, username, telaio) 
                VALUES (?, ?, ?, 'Completato', ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sdsss", $codice, $prezzo, $metodo, $username, $telaio);
        return $stmt->execute();
    }

    public function updateVehicleStatus($telaio, $nuovoStato) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE Veicolo SET stato = ? WHERE telaio = ?");
        $stmt->bind_param("ss", $nuovoStato, $telaio);
        return $stmt->execute();
    }
}