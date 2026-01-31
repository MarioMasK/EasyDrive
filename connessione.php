<?php
// Parametri di connessione
$host = "localhost";
$user = "ingegnere"; 
$pass = "ingegnere"; 
$db   = "easydrive_db";

// Creazione della connessione
$conn = new mysqli($host, $user, $pass, $db);

// Controllo errori
if ($conn->connect_error) {
    // Se c'è un errore, ferma tutto e spiega perché
    die("Errore critico di connessione al database: " . $conn->connect_error);
}

// Opzionale: Imposta il set di caratteri a utf8mb4 per gestire correttamente le emoji o accenti
$conn->set_charset("utf8mb4");

?>