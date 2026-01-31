<?php
require_once 'connessione.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['utente_id']) || empty($_SESSION['carrello'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$metodo = "Carta di Credito";
$errori = 0;

// Iniziamo una transazione SQL per garantire la coerenza dei dati
$conn->begin_transaction();

try {
    foreach ($_SESSION['carrello'] as $telaio) {
        $telaio = $conn->real_escape_string($telaio);
        
        // 1. Recuperiamo il prezzo attuale del veicolo
        $res = $conn->query("SELECT prezzoVendita FROM Veicolo WHERE telaio = '$telaio'");
        $veicolo = $res->fetch_assoc();
        $prezzo = $veicolo['prezzoVendita'];
        
        // 2. Generiamo codice univoco per l'ordine
        $codice_univoco = "ORD-" . strtoupper(uniqid());

        // 3. Inserimento in Ordine_Vendita
        $sql_ordine = "INSERT INTO Ordine_Vendita (codice_univoco, prezzo_finale, metodo_pagamento, stato_pagamento, username, telaio) 
                       VALUES ('$codice_univoco', $prezzo, '$metodo', 'Completato', '$username', '$telaio')";
        
        if (!$conn->query($sql_ordine)) throw new Exception("Errore inserimento ordine");

        // 4. Aggiornamento stato Veicolo
        $sql_update = "UPDATE Veicolo SET stato = 'Venduto' WHERE telaio = '$telaio'";
        
        if (!$conn->query($sql_update)) throw new Exception("Errore aggiornamento veicolo");
    }

    // Se tutto è andato bene, confermiamo i cambiamenti
    $conn->commit();
    
    // Pulizia carrello
    unset($_SESSION['carrello']);
    $_SESSION['messaggio_successo'] = "Pagamento completato con successo! Grazie per aver scelto EasyDrive.";
    header("Location: index.php?status=success");

} catch (Exception $e) {
    // Se c'è un errore, annulliamo tutto (Rollback)
    $conn->rollback();
    echo "Errore durante l'elaborazione dell'ordine: " . $e->getMessage();
}

exit();
?>