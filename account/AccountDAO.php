<?php
namespace it\unisa\easydrive\account;
use it\unisa\easydrive\core\Database;

class AccountDAO {
    public function getAccountByIdentifier($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT AccountId, username, nome, ruolo, hashedPassword FROM Account WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $id, $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }



        
    public function getWishlistByUser($username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT telaio FROM Wishlist_Interesse WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function removeWishlistItem($username, $telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM Wishlist_Interesse WHERE username = ? AND telaio = ?");
        $stmt->bind_param("ss", $username, $telaio);
        return $stmt->execute();
    }
    
    public function checkVeicoloDisponibile($telaio) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT stato FROM Veicolo WHERE telaio = ? AND stato = 'Disponibile'");
        $stmt->bind_param("s", $telaio);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function esisteGia($username, $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT username FROM Account WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function inserisciAccount($dati) {
    $db = Database::getConnection();
    $sql = "INSERT INTO Account (
                username, hashedPassword, nome, cognome, sesso, 
                email, numeroTelefono, dataNascita, provincia, 
                citta, via, numeroCivico, cap, ruolo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cliente_registrato')";
    
    $stmt = $db->prepare($sql);
    
    
    $stmt->bind_param("sssssssssssss", 
        $dati['username'], 
        $dati['hashedPassword'], 
        $dati['nome'], 
        $dati['cognome'], 
        $dati['sesso'], 
        $dati['email'], 
        $dati['telefono'], 
        $dati['data_nascita'], 
        $dati['provincia'], 
        $dati['citta'], 
        $dati['via'], 
        $dati['civico'], 
        $dati['cap']
    );
    
    return $stmt->execute();
}
    public function getFullAccountByUsername($username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM Account WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

