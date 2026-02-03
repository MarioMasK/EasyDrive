<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'SalesDAO.php';
require_once 'SalesLogic.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$salesLogic = new \it\unisa\easydrive\sales\SalesLogic();
$username = $_SESSION['username'] ?? null;

// Eseguiamo la pulizia e poi il recupero
$salesLogic->sincronizzaEPulisciCarrello($username);
$datiCarrello = $salesLogic->getDettagliCompletiCarrello();

$veicoli_dettaglio = $datiCarrello['lista'];
$totale_acquisto = $datiCarrello['totale'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Il tuo Carrello</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../carrello.css">
</head>
<body class="bg-light">

    <?php include '../header.php'; ?>

    <main class="container my-5" style="min-height: 70vh;">
        
        <?php if (empty($veicoli_dettaglio)): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="empty-cart-card shadow-sm border p-5 bg-white rounded-4">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                        <h2 class="fw-bold text-dark mb-3 mt-3">Carrello Vuoto</h2>
                        <p class="text-muted mb-5">Esplora il catalogo 2026 per trovare la tua prossima auto.</p>
                        <a href="../catalog/catalogo.php" class="btn btn-primary px-5 py-3 rounded-pill fw-bold">Esplora la flotta</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 mb-4">
                    <h2 class="fw-bold"><i class="bi bi-bag-check me-2 text-primary"></i>Riepilogo Ordine</h2>
                    <p class="text-muted">Stai acquistando <?php echo count($veicoli_dettaglio); ?> veicolo/i.</p>
                </div>
                
                <div class="col-lg-8">
                    <?php foreach ($veicoli_dettaglio as $v): ?>
                        <div class="card shadow-sm mb-3 border-0 rounded-3">
                            <div class="card-body d-flex align-items-center">
                                <img src="<?php echo $v['url_immagine']; ?>" class="rounded me-3" style="width: 120px; height: 80px; object-fit: cover;" alt="Veicolo">
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($v['marca'] . " " . $v['modello']); ?></h5>
                                    <span class="text-muted small">Telaio: <?php echo $v['telaio']; ?></span>
                                </div>
                                <div class="text-end me-4">
                                    <span class="d-block fw-bold fs-5 text-dark">€<?php echo number_format($v['prezzoVendita'], 2, ',', '.'); ?></span>
                                </div>
                                <a href="rimuovi_dal_carrello.php?id=<?php echo urlencode($v['telaio']); ?>" class="btn btn-link text-danger"><i class="bi bi-trash3-fill fs-4"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-lg p-4 bg-white border-0 rounded-4">
                        <h4 class="fw-bold mb-3">Totale</h4>
                        <hr>
                        <div class="d-flex justify-content-between h4 fw-bold mb-4">
                            <span>Totale:</span>
                            <span>€<?php echo number_format($totale_acquisto, 2, ',', '.'); ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="../payment/Payment.php" class="btn btn-success w-100 py-3 mb-3 fw-bold shadow-sm">PROCEDI AL PAGAMENTO</a>
                        <?php else: ?>
                            <a href="../account/login.php" class="btn btn-warning w-100 py-3 mb-3 fw-bold shadow-sm">ACCEDI PER ORDINARE</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../footer.php'; ?>
    <script src="../header.js"></script>
</body>
</html>