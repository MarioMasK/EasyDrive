<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../sales/SalesDAO.php';
require_once __DIR__ . '/../sales/SalesLogic.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Controllo Accesso tramite logica
if (!isset($_SESSION['username'])) {
    header("Location: ../account/login.php");
    exit();
}
if (empty($_SESSION['carrello'])) {
    header("Location: ../sales/carrello.php");
    exit();
}

// 2. Recupero Totale tramite SalesLogic
$salesDAO = new \it\unisa\easydrive\sales\SalesDAO();
$totale = $salesDAO->getCartTotal($_SESSION['carrello']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Sicuro - EasyDrive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
</head>
<body class="bg-light">
    <?php include '../header.php'; ?>

    <div class="container py-5">
        <div class="card mx-auto shadow-lg border-0 rounded-4 overflow-hidden" style="max-width: 600px;">
            <div class="bg-dark p-4 text-white text-center">
                <h3 class="fw-bold mb-0">Pagamento Sicuro</h3>
                <p class="small opacity-75 mb-0">Transazione crittografata 2026</p>
            </div>
            
            <div class="p-5">
                <div class="alert alert-secondary d-flex justify-content-between align-items-center mb-4">
                    <span>Totale Ordine:</span>
                    <span class="h4 fw-bold mb-0">€<?php echo number_format($totale, 2, ',', '.'); ?></span>
                </div>

                <form action="../sales/conferma_ordine.php" method="POST" id="paymentForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Titolare Carta</label>
                        <input type="text" name="titolare" class="form-control" placeholder="Nome e Cognome" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Numero Carta</label>
                        <input type="text" name="numero_carta" class="form-control" pattern="\d{16}" maxlength="16" placeholder="16 cifre" required>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="form-label fw-bold">Scadenza (MM/AA)</label>
                            <input type="text" name="scadenza" id="scadenza" class="form-control" placeholder="MM/AA" maxlength="5" required>
                            <div id="expiryError" class="text-danger small mt-1" style="display:none;">Carta scaduta o non valida.</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">CVV</label>
                            <input type="password" name="cvv" class="form-control" pattern="\d{3}" maxlength="3" placeholder="123" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mt-4 py-3 fw-bold rounded-3 shadow">
                        CONFERMA E PAGA ORA
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('paymentForm').onsubmit = function(e) {
        const input = document.getElementById('scadenza').value;
        const error = document.getElementById('expiryError');
        const parts = input.split('/');
        
        if(parts.length !== 2) { e.preventDefault(); return; }
        
        const dataUser = new Date(2000 + parseInt(parts[1]), parseInt(parts[0]) - 1, 1);
        const oggi = new Date();
        const meseProssimo = new Date(oggi.getFullYear(), oggi.getMonth() + 1, 1);
        
        if (dataUser < meseProssimo) {
            e.preventDefault();
            error.style.display = 'block';
            document.getElementById('scadenza').classList.add('is-invalid');
        }
    };
    </script>
</body>
</html>