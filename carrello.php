<?php
require_once 'connessione.php';

// Avvia la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. PULIZIA CARRELLO DINAMICA
if (isset($_SESSION['carrello']) && !empty($_SESSION['carrello'])) {
    foreach ($_SESSION['carrello'] as $key => $t_sessione) {
        $t_clean = $conn->real_escape_string($t_sessione);
        
        // Verifichiamo se il veicolo è ancora 'Disponibile'
        $v_check = $conn->query("SELECT stato FROM Veicolo WHERE telaio = '$t_clean' AND stato = 'Disponibile'");
        
        if (!$v_check || $v_check->num_rows == 0) {
            // Rimosso perché non più disponibile
            unset($_SESSION['carrello'][$key]);
            
            // Rimosso dal database se l'utente è loggato
            if (isset($_SESSION['username'])) {
                $u_log = $conn->real_escape_string($_SESSION['username']);
                $conn->query("DELETE FROM Wishlist_Interesse WHERE username = '$u_log' AND telaio = '$t_clean'");
            }
        }
    }
    // Re-indicizziamo l'array
    $_SESSION['carrello'] = array_values($_SESSION['carrello']);
}

$veicoli_dettaglio = [];
$totale_acquisto = 0;

// 2. RECUPERO DATI PER LA VISUALIZZAZIONE
if (isset($_SESSION['carrello']) && !empty($_SESSION['carrello'])) {
    $lista_telai = "'" . implode("','", array_map([$conn, 'real_escape_string'], $_SESSION['carrello'])) . "'";

    $sql = "SELECT v.*, i.url_immagine 
            FROM Veicolo v 
            LEFT JOIN Immagine_Veicolo i ON v.telaio = i.telaio_veicolo 
            WHERE v.telaio IN ($lista_telai) AND (i.is_principale = 1 OR i.is_principale IS NULL)";

    $risultato = $conn->query($sql);
    
    if ($risultato) {
        while ($row = $risultato->fetch_assoc()) {
            $veicoli_dettaglio[] = $row;
            $totale_acquisto += $row['prezzoVendita'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Il tuo Carrello</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="carrello.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container my-5" style="min-height: 70vh;">
        
        <?php if (empty($veicoli_dettaglio)): ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="empty-cart-card shadow-sm border p-5 bg-white rounded-4">
                        <i class="bi bi-cart-x empty-cart-icon"></i>
                        <h2 class="fw-bold text-dark mb-3 mt-3">Carrello Vuoto</h2>
                        <p class="text-muted mb-5 px-md-5">
                            Non hai veicoli salvati o quelli che avevi scelto sono stati venduti. Esplora il catalogo 2026 per trovare la tua prossima auto.
                        </p>
                        <a href="catologo.php" class="btn btn-go-home px-5 py-3 rounded-pill fw-bold">
                            <i class="bi bi-search me-2"></i> Esplora la flotta
                        </a>
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
                    <?php foreach ($veicoli_dettaglio as $v): 
                        $img = str_replace('dl=0', 'raw=1', $v['url_immagine'] ?? 'https://placehold.co/400x300');
                    ?>
                        <div class="card cart-card shadow-sm mb-3 border-0">
                            <div class="card-body d-flex align-items-center">
                                <img src="<?php echo $img; ?>" class="cart-img me-3 shadow-sm" alt="Veicolo">
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($v['marca'] . " " . $v['modello']); ?></h5>
                                    <span class="text-muted small">Telaio: <?php echo $v['telaio']; ?></span>
                                </div>
                                <div class="text-end me-4">
                                    <span class="d-block fw-bold fs-5" style="color: var(--accent-orange);">€<?php echo number_format($v['prezzoVendita'], 2, ',', '.'); ?></span>
                                    <small class="text-muted">Prezzo Acquisto</small>
                                </div>
                                
                                <a href="rimuovi_dal_carrello.php?id=<?php echo urlencode($v['telaio']); ?>" class="btn btn-link text-danger p-0" title="Rimuovi">
                                    <i class="bi bi-trash3-fill fs-4"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card checkout-sidebar shadow-lg p-4 bg-white border-0 rounded-4">
                        <h4 class="fw-bold mb-3">Totale</h4>
                        <hr>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Articoli:</span>
                            <span><?php echo count($veicoli_dettaglio); ?></span>
                        </div>
                        <div class="d-flex justify-content-between h4 fw-bold mb-4">
                            <span>Totale:</span>
                            <span class="text-dark">€<?php echo number_format($totale_acquisto, 2, ',', '.'); ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="pagamento.php" class="btn btn-checkout w-100 py-3 mb-3 fw-bold text-white shadow-sm">
                                PROCEDI AL PAGAMENTO <i class="bi bi-credit-card ms-2"></i>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-checkout w-100 py-3 mb-3 fw-bold text-white shadow-sm" style="background-color: var(--accent-orange);">
                                ACCEDI PER ORDINARE <i class="bi bi-person-lock ms-2"></i>
                            </a>
                        <?php endif; ?>
                        
                        <p class="small text-center text-muted mb-0">
                            <i class="bi bi-shield-lock me-1"></i> Transazione Protetta 2026
                        </p>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </main>

    <?php include 'footer.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="header.js"></script>
</body>
</html>