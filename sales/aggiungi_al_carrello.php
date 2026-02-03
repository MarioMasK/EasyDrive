<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'SalesDAO.php';
require_once 'SalesLogic.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $salesLogic = new \it\unisa\easydrive\sales\SalesLogic();
    $username = $_SESSION['username'] ?? null;
    
    $risultato = $salesLogic->aggiungiAlCarrello($_GET['id'], $username);

    // Salviamo il messaggio per mostrarlo nella pagina successiva
    $_SESSION['messaggio_' . $risultato['status']] = $risultato['msg'];
}

header("Location: carrello.php");
exit();
?>