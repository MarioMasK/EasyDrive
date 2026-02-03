<?php
namespace it\unisa\easydrive\sales;
use it\unisa\easydrive\core\Database;
class SalesLogic {
    private $dao;
    public function __construct() { $this->dao = new SalesDAO(); }

    public function aggiungiAlCarrello($telaio, $username = null) {
        // 1. Validazione Disponibilità
        if (!$this->dao->checkDisponibilita($telaio)) {
            return ['status' => 'error', 'msg' => 'Veicolo non più disponibile.'];
        }

        // 2. Gestione Sessione (Sempre)
        if (!isset($_SESSION['carrello'])) { $_SESSION['carrello'] = array(); }
        if (!in_array($telaio, $_SESSION['carrello'])) {
            $_SESSION['carrello'][] = $telaio;
        }

        // 3. Gestione Database (Solo se loggato)
        if ($username) {
            if (!$this->dao->existsInteresse($username, $telaio)) {
                $this->dao->insertInteresse($username, $telaio);
            }
            return ['status' => 'success', 'msg' => 'Veicolo salvato nel tuo account e nel carrello.'];
        }

        return ['status' => 'info', 'msg' => 'Veicolo aggiunto al carrello temporaneo. Accedi per salvarlo.'];
    }
    public function sincronizzaEPulisciCarrello($username = null) {
        if (!isset($_SESSION['carrello']) || empty($_SESSION['carrello'])) return;

        foreach ($_SESSION['carrello'] as $key => $telaio) {
            $stato = $this->dao->getVehicleStatus($telaio);
            
            if ($stato !== 'Disponibile') {
                unset($_SESSION['carrello'][$key]);
                if ($username) {
                    $this->dao->removeFromWishlist($username, $telaio);
                }
            }
        }
        $_SESSION['carrello'] = array_values($_SESSION['carrello']);
    }

    public function getDettagliCompletiCarrello() {
        if (!isset($_SESSION['carrello']) || empty($_SESSION['carrello'])) return ['lista' => [], 'totale' => 0];

        $result = $this->dao->getVehiclesDetails($_SESSION['carrello']);
        $veicoli = [];
        $totale = 0;

        while ($row = $result->fetch_assoc()) {
            // Formattazione immagine (Logica Presentation-ready)
            $row['url_immagine'] = str_replace('dl=0', 'raw=1', $row['url_immagine'] ?? 'https://placehold.co/400x300');
            $veicoli[] = $row;
            $totale += $row['prezzoVendita'];
        }

        return ['lista' => $veicoli, 'totale' => $totale];
    }
    public function rimuoviVeicolo($telaio, $username = null) {
        // 1. Rimozione dalla SESSIONE
        if (isset($_SESSION['carrello'])) {
            $index = array_search($telaio, $_SESSION['carrello']);
            if ($index !== false) {
                unset($_SESSION['carrello'][$index]);
                // Re-indicizziamo per evitare "buchi" nell'array
                $_SESSION['carrello'] = array_values($_SESSION['carrello']);
            }
        }

        // 2. Rimozione dal DATABASE (se loggato)
        if ($username) {
            $this->dao->removeFromWishlist($username, $telaio);
        }

        return true;
    }
    public function finalizzaAcquisto($carrello, $username) {
        $db = Database::getConnection();
        $db->begin_transaction();

        try {
            foreach ($carrello as $telaio) {
                // 1. Recupero prezzo
                $prezzo = $this->dao->getVehiclePrice($telaio);
                
                // 2. Generazione codice univoco (Business Logic)
                $codice = "ORD-" . strtoupper(uniqid());
                $metodo = "Carta di Credito";

                // 3. Creazione record ordine
                if (!$this->dao->createOrderRecord($codice, $prezzo, $metodo, $username, $telaio)) {
                    throw new \Exception("Errore inserimento ordine per telaio: $telaio");
                }

                // 4. Aggiornamento veicolo
                if (!$this->dao->updateVehicleStatus($telaio, 'Venduto')) {
                    throw new \Exception("Errore aggiornamento stato veicolo: $telaio");
                }
                
                // 5. Pulizia facoltativa della wishlist DB
                $this->dao->removeFromWishlist($username, $telaio);
            }

            $db->commit();
            unset($_SESSION['carrello']); // Pulizia memoria temporanea
            return true;

        } catch (\Exception $e) {
            $db->rollback();
            return $e->getMessage();
        }
    }
    public function ottieniStoricoUtente($username) {
        if (!$username) return null;
        return $this->dao->getHistoryByUsername($username);
    }
}