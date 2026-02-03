<?php
require_once 'AccountLogic.php';


// Inizializziamo la logica
$accountLogic = new \it\unisa\easydrive\account\AccountLogic();

// Eseguiamo il logout tramite il Logic Tier
$accountLogic->logout();

// Reindirizziamo alla Home (usando ../ per uscire dalla cartella account)
header("Location: ../index.php?logout=1");
exit();
?>