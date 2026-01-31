<?php
require_once 'connessione.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Verifichiamo se l'ID è presente
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $telaio_rimuovere = $conn->real_escape_string($_GET['id']);

    // 2. Rimozione dalla SESSIONE
    if (isset($_SESSION['carrello'])) {
        // Cerchiamo la posizione dell'ID nell'array
        $index = array_search($telaio_rimuovere, $_SESSION['carrello']);
        
        if ($index !== false) {
            unset($_SESSION['carrello'][$index]);
            // Re-indicizziamo l'array
            $_SESSION['carrello'] = array_values($_SESSION['carrello']);
        }
    }

    // 3. Rimozione dal DATABASE (se l'utente è loggato)
    if (isset($_SESSION['username'])) {
        $user_loggato = $conn->real_escape_string($_SESSION['username']);
        
        // Query per cancellare l'associazione persistente
        $sql_delete = "DELETE FROM Wishlist_Interesse 
                       WHERE username = '$user_loggato' 
                       AND telaio = '$telaio_rimuovere'";
        
        $conn->query($sql_delete);
    }
    
    $_SESSION['messaggio_info'] = "Veicolo rimosso correttamente dal carrello.";
}

// 4. Ritorno al carrello
header("Location: carrello.php");
exit();
?>