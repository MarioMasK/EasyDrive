<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'BookingDAO.php';
require_once 'BookingLogic.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Controllo Accesso
if (!isset($_SESSION['username'])) {
    header("Location: ../account/login.php");
    exit();
}

$telaio = $_GET['telaio'] ?? '';
$bookingLogic = new \it\unisa\easydrive\booking\BookingLogic();
$v = $bookingLogic->getDatiVeicoloPerNoleggio($telaio);

if (!$v) {
    header("Location: ../catalog/catalogo.php");
    exit();
}

// Gestione errori sessione
$errore = $_SESSION['errore_date'] ?? null;
unset($_SESSION['errore_date']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>EasyDrive - Prenota Noleggio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../header.css">
</head>
<body class="bg-light">
    <?php include '../header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <?php if ($errore): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Errore:</strong> <?php echo $errore; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-3">Richiesta di Noleggio</h3>
                        <p class="text-muted">Veicolo: <strong><?php echo $v['marca'] . " " . $v['modello']; ?></strong></p>
                        
                        <form id="formNoleggio" action="noleggio_process.php" method="POST">
                            <input type="hidden" name="telaio" value="<?php echo htmlspecialchars($telaio); ?>">
                            
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">Data Inizio</label>
                                    <input type="date" id="data_inizio" name="data_inizio" class="form-control" 
                                           required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col">
                                    <label class="form-label">Data Fine</label>
                                    <input type="date" id="data_fine" name="data_fine" class="form-control" required>
                                    <div class="invalid-feedback">Data non valida.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Luogo di Ritiro</label>
                                <input type="text" name="luogo_ritiro" class="form-control" placeholder="Es. Sede Salerno" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Luogo di Consegna</label>
                                <input type="text" name="luogo_consegna" class="form-control" placeholder="Es. Aeroporto Napoli" required>
                            </div>

                            <div class="p-3 bg-light rounded mb-4 text-center">
                                <small class="text-muted d-block text-uppercase fw-bold">Tariffa Giornaliera</small>
                                <span class="h5 fw-bold text-primary">€ <?php echo number_format($v['tariffaNoleggioGiorno'], 2, ',', '.'); ?></span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                                CONFERMA PRENOTAZIONE
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const inputInizio = document.getElementById('data_inizio');
        const inputFine = document.getElementById('data_fine');
        
        inputInizio.addEventListener('change', function() {
            if (this.value) {
                inputFine.min = this.value;
                if (inputFine.value && inputFine.value < this.value) inputFine.value = '';
            }
        });
    </script>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>