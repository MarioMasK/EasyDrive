<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'SalesDAO.php';
require_once 'SalesLogic.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Controllo sessione
if (!isset($_SESSION['utente_id']) || empty($_SESSION['carrello'])) {
    header("Location: ../index.php");
    exit();
}

$salesLogic = new \it\unisa\easydrive\sales\SalesLogic();
$risultato = $salesLogic->finalizzaAcquisto($_SESSION['carrello'], $_SESSION['username']);

if ($risultato === true) {
    $_SESSION['messaggio_successo'] = "Pagamento completato! Grazie per aver scelto EasyDrive.";
    header("Location: ../index.php?status=success");
} else {
    // In caso di errore mostriamo il rollback message
    $_SESSION['messaggio_errore'] = "Errore critico: " . $risultato;
    header("Location: carrello.php");
}
exit();