<?php
require_once 'connessione.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verifichiamo che l'ID del veicolo sia presente
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $telaio = $conn->real_escape_string($_GET['id']);

    // 2. Controllo di sicurezza: il veicolo esiste ed è davvero disponibile?
    $check = $conn->query("SELECT stato FROM Veicolo WHERE telaio = '$telaio' AND stato = 'Disponibile'");
    
    if ($check && $check->num_rows > 0) {
        
        // Inizializziamo il carrello in sessione se non esiste
        if (!isset($_SESSION['carrello'])) {
            $_SESSION['carrello'] = array();
        }

        // 3. Salvataggio in Sessione (per tutti gli utenti)
        if (!in_array($telaio, $_SESSION['carrello'])) {
            $_SESSION['carrello'][] = $telaio;
        }

        // 4. Salvataggio nel Database (solo se l'utente è loggato)
        if (isset($_SESSION['username'])) {
            $user = $_SESSION['username'];
            
            // Verifichiamo se l'associazione esiste già nel DB per evitare duplicati
            $check_db = $conn->query("SELECT id_interesse FROM Wishlist_Interesse WHERE username = '$user' AND telaio = '$telaio'");
            
            if ($check_db->num_rows == 0) {
                $ins_sql = "INSERT INTO Wishlist_Interesse (username, telaio) VALUES ('$user', '$telaio')";
                $conn->query($ins_sql);
            }
            $_SESSION['messaggio_successo'] = "Veicolo salvato nel tuo account e aggiunto al carrello.";
        } else {
            $_SESSION['messaggio_info'] = "Veicolo aggiunto al carrello temporaneo. Accedi per salvarlo permanentemente.";
        }

    } else {
        $_SESSION['messaggio_errore'] = "Spiacenti, il veicolo non è più disponibile o è stato rimosso.";
    }
}

// 5. Reindirizziamo l'utente alla pagina del carrello
header("Location: carrello.php");
exit();
?>