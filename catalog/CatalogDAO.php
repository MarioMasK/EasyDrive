<?php
namespace it\unisa\easydrive\catalog;
use it\unisa\easydrive\core\Database;

class CatalogDAO {
    // Recupera i primi 4 veicoli disponibili con immagine principale
    public function getFeaturedVehicles($limit = 4) {
        $db = Database::getConnection();
        $sql = "SELECT v.*, i.url_immagine 
                FROM Veicolo v 
                LEFT JOIN Immagine_Veicolo i ON v.telaio = i.telaio_veicolo 
                WHERE v.stato = 'Disponibile' 
                AND (i.is_principale = 1 OR i.is_principale IS NULL)
                LIMIT ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    
    public function getFilteredVehicles($filters) {
        $db = Database::getConnection();
        
        $sql = "SELECT v.*, i.url_immagine 
                FROM Veicolo v 
                LEFT JOIN Immagine_Veicolo i ON v.telaio = i.telaio_veicolo AND i.is_principale = 1 
                WHERE 1=1";

        // Filtri dinamici
        if (!empty($filters['nome'])) {
            $nome = $db->real_escape_string($filters['nome']);
            $sql .= " AND (v.marca LIKE '%$nome%' OR v.modello LIKE '%$nome%')";
        }
        if (!empty($filters['min'])) {
            $sql .= " AND v.prezzoVendita >= " . (float)$filters['min'];
        }
        if (!empty($filters['max'])) {
            $sql .= " AND v.prezzoVendita <= " . (float)$filters['max'];
        }
        if ($filters['disponibile']) {
            $sql .= " AND v.stato = 'Disponibile'";
        }
        if ($filters['noleggio']) {
            $sql .= " AND v.tariffaNoleggioGiorno > 0";
        }

        // Ordinamento
        switch ($filters['ordine']) {
            case 'prezzo_asc': $sql .= " ORDER BY v.prezzoVendita ASC"; break;
            case 'prezzo_desc': $sql .= " ORDER BY v.prezzoVendita DESC"; break;
            default: $sql .= " ORDER BY v.marca ASC"; break;
        }

        return $db->query($sql);
    }
    
    public function getVeicoloByTelaio($telaio) {
        $db = Database::getConnection();
        $sql = "SELECT v.*, i.url_immagine 
                FROM Veicolo v 
                LEFT JOIN Immagine_Veicolo i ON v.telaio = i.telaio_veicolo 
                WHERE v.telaio = ? AND (i.is_principale = 1 OR i.is_principale IS NULL)
                LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $telaio);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
}