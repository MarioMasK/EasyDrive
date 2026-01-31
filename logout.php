<?php
session_start(); // Entriamo nella sessione esistente

// 1. Svuotiamo l'array $_SESSION
$_SESSION = array();

// 2. Se si desidera distruggere completamente la sessione, 
// bisogna cancellare anche il cookie di sessione.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Distruggiamo la sessione sul server
session_destroy();

// 4. Reindirizziamo alla Home con un segnale di successo
header("Location: index.php?logout=1");
exit();
?>