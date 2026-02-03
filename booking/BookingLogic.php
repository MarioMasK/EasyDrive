<?php
namespace it\unisa\easydrive\booking;

class BookingLogic {
    private $dao;
    public function __construct() { $this->dao = new BookingDAO(); }


public function processaPrenotazione($dati, $username) {
    // 1. VALIDAZIONE: Controlliamo che i dati fondamentali ci siano
    if (empty($dati['telaio']) || empty($dati['data_inizio']) || empty($dati['data_fine'])) {
        return ['success' => false, 'error' => "Dati incompleti. Compilare tutti i campi."];
    }

    // 2. LOGICA DELLE DATE: Calcoliamo la durata del noleggio
    $inizio = new \DateTime($dati['data_inizio']);
    $fine = new \DateTime($dati['data_fine']);
    $oggi = new \DateTime();

    if ($inizio < $oggi->modify('-1 minute')) { // Piccolo margine per evitare errori di secondi
        return ['success' => false, 'error' => "La data di inizio non può essere nel passato."];
    }
    if ($fine <= $inizio) {
        return ['success' => false, 'error' => "La data di fine deve essere successiva a quella di inizio."];
    }

    // 3. CONTROLLO COLLISIONI: Verifichiamo se l'auto è già impegnata
    // Usiamo il metodo che hai già nel DAO
    $isOccupato = $this->dao->checkCollisioniDate($dati['telaio'], $dati['data_inizio'], $dati['data_fine']);
    if ($isOccupato) {
        return ['success' => false, 'error' => "Spiacenti, il veicolo è già prenotato per queste date."];
    }

    // 4. RECUPERO TARIFFA: Usiamo il metodo corretto getVehicleRate
    $veicoloInfo = $this->dao->getVehicleRate($dati['telaio']);
    if (!$veicoloInfo) {
        return ['success' => false, 'error' => "Veicolo non trovato."];
    }

    // Calcolo giorni (minimo 1 giorno) e prezzo totale
    $intervallo = $inizio->diff($fine);
    $giorni = $intervallo->days ?: 1;
    $prezzoTotale = $giorni * $veicoloInfo['tariffaNoleggioGiorno'];

    // 5. PREPARAZIONE DATI PER IL DB (Allineamento chiavi col tuo DAO)
    $datiFinali = [
        'data_inizio'     => $dati['data_inizio'],
        'data_fine'       => $dati['data_fine'],
        'luogo_ritiro'    => $dati['luogo_ritiro'] ?? 'Sede Centrale',
        'luogo_consegna'  => $dati['luogo_consegna'] ?? 'Sede Centrale',
        'prezzo_stimato'  => $prezzoTotale,
        'username'        => $username,
        'telaio'          => $dati['telaio']
    ];

    // 6. SALVATAGGIO: Chiamiamo il DAO per inserire il record
    $esito = $this->dao->insertPrenotazione($datiFinali);

    if ($esito) {
        return [
            'success' => true, 
            'prezzo' => $prezzoTotale, 
            'giorni' => $giorni,
            'marca' => $veicoloInfo['marca'],
            'modello' => $veicoloInfo['modello']
        ];
    } else {
        return ['success' => false, 'error' => "Errore tecnico durante il salvataggio."];
    }
}
    public function getDatiVeicoloPerNoleggio($telaio) {
        if (empty($telaio)) return null;
        
        $veicolo = $this->dao->getVehicleRate($telaio);
        if (!$veicolo) return null;


        
        return $veicolo;
    }
}
