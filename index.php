<?php 
// Inclusione dei componenti dell'architettura Three-Tier
require_once 'core/Database.php';
require_once 'catalog/CatalogDAO.php';
require_once 'catalog/CatalogLogic.php';

// Far partire la sessione per i messaggi e il login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inizializzazione della logica di business
$catalogLogic = new \it\unisa\easydrive\catalog\CatalogLogic();
$risultato = $catalogLogic->getHomePageHighlights();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <?php if (isset($_GET['logout']) && $_GET['logout'] == 1): ?>
        <div class="container mt-3">
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        Sei uscito correttamente. Grazie per aver scelto <strong>EasyDrive</strong>!
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <header class="hero-section text-center py-5 bg-light border-bottom">
        <div class="container py-5">
            <?php if (isset($_SESSION['nome'])): ?>
                <h2 class="h4 text-primary mb-3">Bentornato, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h2>
            <?php endif; ?>
            <h1 class="display-3 fw-bold">Mobilità Intelligente 2026</h1>
            <p class="lead mb-4 text-secondary">Soluzioni avanzate di vendita e noleggio basate su ingegneria dei dati.</p>
            <a href="#catalogo" class="btn btn-primary btn-lg px-5 shadow-sm">Esplora la Flotta</a>
        </div>
    </header>

    <main class="container my-5" id="catalogo">
        <div class="text-center mb-5">
            <h2 class="fw-bold">I Nostri Veicoli Premium</h2>
            <p class="text-muted">Selezionati per prestazioni, sicurezza e comfort.</p>
        </div>

        <div class="row">
            <?php 
            if ($risultato && $risultato->num_rows > 0): 
                while($v = $risultato->fetch_assoc()): 
                    $marca = htmlspecialchars($v['marca']);
                    $modello = htmlspecialchars($v['modello']);
                    $categoria = htmlspecialchars($v['categoria']);
                    $descrizione = htmlspecialchars($v['descrizione']);
                    $img_url = str_replace('dl=0', 'raw=1', $v['url_immagine'] ?? 'https://placehold.co/600x400?text=Immagine+Non+Disponibile');
            ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0 transition-hover">
                            <span class="badge bg-dark position-absolute m-3 top-0 start-0 z-1"><?php echo $categoria; ?></span>
                            <div class="card-img-container" style="height: 200px; overflow: hidden; border-radius: 8px 8px 0 0;">
                                <img src="<?php echo $img_url; ?>" class="card-img-top h-100 w-100" style="object-fit: cover;" alt="<?php echo $marca; ?>">
                            </div>
                            <div class="card-body px-3 pb-3 d-flex flex-column">
                                <div class="mb-2">
                                    <h5 class="card-title fw-bold mb-0"><?php echo "$marca $modello"; ?></h5>
                                    <small class="text-muted"><?php echo $v['annoImmatricolazione']; ?> • <?php echo number_format($v['chilometraggio'], 0, ',', '.'); ?> km</small>
                                </div>
                                <div class="row g-2 my-2 text-center" style="font-size: 0.85rem;">
                                    <div class="col-4 border-end"><i class="bi bi-gear-wide-connected d-block text-primary"></i><?php echo htmlspecialchars($v['tipoCambio']); ?></div>
                                    <div class="col-4 border-end"><i class="bi bi-people d-block text-primary"></i><?php echo $v['numeroPosti']; ?>P</div>
                                    <div class="col-4"><i class="bi bi-fuel-pump d-block text-primary"></i><?php echo htmlspecialchars($v['tipoAlimentazione']); ?></div>
                                </div>
                                <p class="card-text small text-secondary mt-3"><?php echo (strlen($descrizione) > 60) ? substr($descrizione, 0, 60).'...' : $descrizione; ?></p>
                                <div class="mt-auto">
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div><span class="d-block text-muted small">Acquisto</span><span class="h6 fw-bold text-dark">€<?php echo number_format($v['prezzoVendita'], 2, ',', '.'); ?></span></div>
                                        <div class="text-end border-start ps-3"><span class="d-block text-muted small">Noleggio</span><span class="h6 fw-bold text-primary">€<?php echo number_format($v['tariffaNoleggioGiorno'], 2, ',', '.'); ?>/gg</span></div>
                                    </div>
                                    <a href="catalog/dettaglio_veicolo.php?id=<?php echo $v['telaio']; ?>" class="btn btn-outline-dark w-100 btn-sm fw-bold">Dettagli</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <p class="lead mt-3">Al momento non ci sono veicoli disponibili.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="header.js"></script> 
</body>
</html>