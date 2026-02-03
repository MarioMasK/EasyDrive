<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'CatalogDAO.php';
require_once 'CatalogLogic.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$catalogLogic = new \it\unisa\easydrive\catalog\CatalogLogic();
$risultato = $catalogLogic->ricercaVeicoli($_GET);

// Variabili per mantenere i valori nel form
$filtro_nome = $_GET['nome'] ?? '';
$prezzo_min = $_GET['min'] ?? '';
$prezzo_max = $_GET['max'] ?? '';
$solo_disponibili = isset($_GET['disponibile']);
$solo_noleggiabili = isset($_GET['noleggio']);
$ordinamento = $_GET['ordine'] ?? 'DEFAULT';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Catalogo Veicoli - EasyDrive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../catologo.css">
    <style>
        .rental-price { color: #0d6efd; font-weight: bold; font-size: 0.9rem; }
        .vehicle-card { display: flex; flex-direction: column; height: 100%; transition: transform 0.2s; }
        .vehicle-card:hover { transform: translateY(-5px); }
        .card-content { flex-grow: 1; display: flex; flex-direction: column; }
        .actions { margin-top: auto; }
        .btn-noleggia { background-color: #f8f9fa; border: 1px solid #0d6efd; color: #0d6efd; }
    </style>
</head>
<body class="bg-light">

    <?php include '../header.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <button id="toggleFilters" class="btn d-md-none mb-3 btn-dark">
                <i class="bi bi-filter"></i> Mostra Filtri
            </button>

            <aside id="sidebarFiltri" class="col-md-3">
                <div class="filter-box shadow-sm bg-white p-4 rounded-3 border">
                    <h4 class="fw-bold mb-3">Filtra Ricerca</h4>
                    <form method="GET" action="catalogo.php">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Modello/Marca</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($filtro_nome); ?>">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label small fw-bold">Min (€)</label>
                                <input type="number" name="min" class="form-control" value="<?php echo $prezzo_min; ?>">
                            </div>
                            <div class="col">
                                <label class="form-label small fw-bold">Max (€)</label>
                                <input type="number" name="max" class="form-control" value="<?php echo $prezzo_max; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Ordina per</label>
                            <select name="ordine" class="form-select">
                                <option value="DEFAULT">A-Z</option>
                                <option value="prezzo_asc" <?php if($ordinamento == 'prezzo_asc') echo 'selected'; ?>>Prezzo Crescente</option>
                                <option value="prezzo_desc" <?php if($ordinamento == 'prezzo_desc') echo 'selected'; ?>>Prezzo Decrescente</option>
                            </select>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="disponibile" id="disponibile" <?php if($solo_disponibili) echo 'checked'; ?>>
                            <label class="form-check-label small" for="disponibile">Solo Disponibili</label>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="noleggio" id="noleggio" <?php if($solo_noleggiabili) echo 'checked'; ?>>
                            <label class="form-check-label small" for="noleggio">Solo per Noleggio</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Applica Filtri</button>
                        <a href="catalogo.php" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </form>
                </div>
            </aside>

            <main class="col-md-9">
                <div class="row">
                    <?php if ($risultato && $risultato->num_rows > 0): ?>
                        <?php while($veicolo = $risultato->fetch_assoc()): 
                            $is_disponibile = ($veicolo['stato'] == 'Disponibile');
                            $img_url = $veicolo['url_immagine'] ?: '../images/placeholder-car.png';
                        ?>
                            <div class="col-sm-6 col-lg-4 mb-4">
                                <div class="vehicle-card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                                    <div class="img-container">
                                        <img src="<?php echo $img_url; ?>" alt="<?php echo $veicolo['modello']; ?>" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
                                    </div>
                                    <div class="card-content p-3">
                                        <span class="text-muted small uppercase fw-bold"><?php echo $veicolo['marca']; ?></span>
                                        <h5 class="fw-bold mb-2"><?php echo $veicolo['modello']; ?></h5>
                                        
                                        <div class="mb-3">
                                            <div class="text-dark fw-bold">Acquisto: € <?php echo number_format($veicolo['prezzoVendita'], 2, ',', '.'); ?></div>
                                            <?php if($veicolo['tariffaNoleggioGiorno'] > 0): ?>
                                                <div class="rental-price">Noleggio: € <?php echo number_format($veicolo['tariffaNoleggioGiorno'], 2, ',', '.'); ?>/gg</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mb-3">
                                            <span class="badge <?php echo $is_disponibile ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
                                                <?php echo $veicolo['stato']; ?>
                                            </span>
                                        </div>

                                        <div class="actions d-flex flex-column gap-2">
                                            <a href="dettaglio_veicolo.php?id=<?php echo $veicolo['telaio']; ?>" class="btn btn-outline-secondary btn-sm">Dettagli</a>
                                            <?php if ($is_disponibile): ?>
                                                <div class="d-flex gap-2">
                                                    <a href="../sales/aggiungi_al_carrello.php?id=<?php echo $veicolo['telaio']; ?>" class="btn btn-success btn-sm flex-fill">Acquista</a>
                                                    <?php if($veicolo['tariffaNoleggioGiorno'] > 0): ?>
                                                        <a href="../booking/prenota_noleggio.php?telaio=<?php echo $veicolo['telaio']; ?>" class="btn btn-noleggia btn-sm flex-fill">Noleggia</a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h3 class="mt-3">Nessun veicolo trovato.</h3>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleFilters').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebarFiltri');
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>