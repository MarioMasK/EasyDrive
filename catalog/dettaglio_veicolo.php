<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'CatalogDAO.php';
require_once 'CatalogLogic.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Controllo ID nell'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$catalogLogic = new \it\unisa\easydrive\catalog\CatalogLogic();
$v = $catalogLogic->fornisceDettagliVeicolo($_GET['id']);

if (!$v) {
    echo "Veicolo non trovato.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - <?php echo htmlspecialchars($v['marca'] . " " . $v['modello']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../index.css"> 
    <style>
        .detail-img { border-radius: 15px; object-fit: cover; width: 100%; max-height: 500px; }
        .spec-icon { font-size: 1.5rem; color: #20B2AA; }
        .price-box { background: #f8f9fa; border-radius: 12px; padding: 20px; }
        .status-badge { font-size: 0.9rem; padding: 8px 15px; border-radius: 50px; }
    </style>
</head>
<body>

    <?php include '../header.php'; ?>

    <main class="container my-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($v['marca']); ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <div class="col-lg-7">
                <img src="<?php echo $v['url_immagine_formattata']; ?>" class="detail-img shadow-lg" alt="<?php echo htmlspecialchars($v['modello']); ?>">
            </div>

            <div class="col-lg-5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-dark"><?php echo htmlspecialchars($v['categoria']); ?></span>
                    <?php if ($v['is_disponibile']): ?>
                        <span class="status-badge bg-success-subtle text-success fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Disponibile
                        </span>
                    <?php else: ?>
                        <span class="status-badge bg-danger-subtle text-danger fw-bold">
                            <i class="bi bi-x-circle-fill me-1"></i> Non Disponibile
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($v['marca'] . " " . $v['modello']); ?></h1>
                
                <div class="price-box mb-4 shadow-sm">
                    <div class="row text-center">
                        <div class="col border-end">
                            <small class="text-muted d-block">Prezzo Vendita</small>
                            <span class="h4 fw-bold text-dark">€<?php echo number_format($v['prezzoVendita'], 2, ',', '.'); ?></span>
                        </div>
                        <div class="col">
                            <small class="text-muted d-block">Tariffa Noleggio</small>
                            <span class="h4 fw-bold text-primary">€<?php echo number_format($v['tariffaNoleggioGiorno'], 2, ',', '.'); ?><small>/gg</small></span>
                        </div>
                    </div>
                </div>

                <p class="text-secondary mb-4">
                    <?php echo nl2br(htmlspecialchars($v['descrizione'])); ?>
                </p>

                <div class="row g-3 mb-5">
                    <div class="col-6 col-md-4 text-center">
                        <div class="p-3 border rounded-3 bg-white">
                            <i class="bi bi-calendar-event spec-icon d-block mb-1"></i>
                            <small class="text-muted">Anno</small>
                            <div class="fw-bold"><?php echo $v['annoImmatricolazione']; ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 text-center">
                        <div class="p-3 border rounded-3 bg-white">
                            <i class="bi bi-speedometer2 spec-icon d-block mb-1"></i>
                            <small class="text-muted">KM</small>
                            <div class="fw-bold"><?php echo number_format($v['chilometraggio'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 text-center">
                        <div class="p-3 border rounded-3 bg-white">
                            <i class="bi bi-gear-wide-connected spec-icon d-block mb-1"></i>
                            <small class="text-muted">Cambio</small>
                            <div class="fw-bold"><?php echo htmlspecialchars($v['tipoCambio']); ?></div>
                        </div>
                    </div>
                </div>

                <?php if ($v['is_disponibile']): ?>
                    <a href="../sales/aggiungi_al_carrello.php?id=<?php echo $v['telaio']; ?>" class="btn btn-primary btn-lg w-100 py-3 shadow fw-bold">
                        <i class="bi bi-cart-plus me-2"></i> Aggiungi al Carrello
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg w-100 py-3 fw-bold" disabled>
                        <i class="bi bi-dash-circle me-2"></i> Veicolo non prenotabile
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>