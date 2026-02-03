<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'BookingDAO.php';
require_once 'BookingLogic.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingLogic = new \it\unisa\easydrive\booking\BookingLogic();
    $risultato = $bookingLogic->processaPrenotazione($_POST, $_SESSION['username']);

    if ($risultato['status'] === 'success') {
        $_SESSION['messaggio_successo'] = "Prenotazione completata con successo!";
        header("Location: ../sales/storico_operazioni.php");
    } else {
        $_SESSION['messaggio_errore'] = $risultato['msg'];
        header("Location: ../catalog/dettaglio_veicolo.php?id=" . $_POST['telaio']);
    }
    exit();
}