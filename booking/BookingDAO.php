<?php
namespace it\unisa\easydrive\booking;
use it\unisa\easydrive\core\Database;

class BookingDAO {

    /**
     * Recupera la tariffa e info base del veicolo.
     */
    public function getVehicleRate($telaio) {
        $db = Database::getConnection();
        $sql = "SELECT marca, modello, tariffaNoleggioGiorno FROM Veicolo WHERE telaio = ? LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $telaio);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Verifica se il veicolo è già impegnato in quelle date.
     */
    public function checkCollisioniDate($telaio, $inizio, $fine) {
        $db = Database::getConnection();
        // Logica: una prenotazione collide se (InizioNuova <= FineEsistente) AND (FineNuova >= InizioEsistente)
        $sql = "SELECT id_prenotazione FROM Prenotazione_Noleggio 
                WHERE telaio = ? 
                AND stato_prenotazione NOT IN ('Annullata', 'Conclusa')
                AND (? <= data_fine AND ? >= data_inizio)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $telaio, $inizio, $fine);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Inserisce la prenotazione e aggiorna lo stato del veicolo (Atomicamente)
     */
    public function insertPrenotazione($dati) {
        $db = Database::getConnection();
        
        // Iniziamo una transazione: o salviamo tutto o nulla
        $db->begin_transaction();

        try {
            // 1. Inserimento Prenotazione (Chiavi allineate alla Logic e al DB)
            $sql = "INSERT INTO Prenotazione_Noleggio 
                    (data_inizio, data_fine, luogo_ritiro, luogo_consegna, prezzo_stimato, stato_prenotazione, username, telaio) 
                    VALUES (?, ?, ?, ?, ?, 'Confermata', ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssssdss", 
                $dati['data_inizio'], 
                $dati['data_fine'], 
                $dati['luogo_ritiro'], 
                $dati['luogo_consegna'], 
                $dati['prezzo_stimato'], 
                $dati['username'], 
                $dati['telaio']
            );
            $stmt->execute();

            // 2. Aggiornamento Stato Veicolo
            $updateSql = "UPDATE Veicolo SET stato = 'InPrenotazione' WHERE telaio = ?";
            $updStmt = $db->prepare($updateSql);
            $updStmt->bind_param("s", $dati['telaio']);
            $updStmt->execute();

            // Se arriviamo qui senza errori, confermiamo tutto
            $db->commit();
            return true;

        } catch (\Exception $e) {
            // Se qualcosa va storto, annulliamo le modifiche al DB
            $db->rollback();
            return false;
        }
    }

    /**
     * Metodo di utility per aggiornamenti manuali dello stato
     */
    public function updateStatoVeicolo($telaio, $stato) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE Veicolo SET stato = ? WHERE telaio = ?");
        $stmt->bind_param("ss", $stato, $telaio);
        return $stmt->execute();
    }
}