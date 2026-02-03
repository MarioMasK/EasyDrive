<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'SalesDAO.php';
require_once 'SalesLogic.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $salesLogic = new \it\unisa\easydrive\sales\SalesLogic();
    $username = $_SESSION['username'] ?? null;
    $telaio = $_GET['id']; // Non serve real_escape_string qui, lo fa il DAO internamente

    if ($salesLogic->rimuoviVeicolo($telaio, $username)) {
        $_SESSION['messaggio_info'] = "Veicolo rimosso correttamente dal carrello.";
    }
}

header("Location: carrello.php");
exit();
?>
