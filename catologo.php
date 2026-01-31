<?php
require_once 'connessione.php';

// Inizializzazione variabili per i filtri
$filtro_nome = $_GET['nome'] ?? '';
$prezzo_min = $_GET['min'] ?? '';
$prezzo_max = $_GET['max'] ?? '';
$solo_disponibili = isset($_GET['disponibile']) ? true : false;
$ordinamento = $_GET['ordine'] ?? 'DEFAULT';

// Costruzione query dinamica
$sql = "SELECT v.*, i.url_immagine 
        FROM Veicolo v 
        LEFT JOIN Immagine_Veicolo i ON v.telaio = i.telaio_veicolo AND i.is_principale = 1 
        WHERE 1=1";

if (!empty($filtro_nome)) {
    $nome_safe = $conn->real_escape_string($filtro_nome);
    $sql .= " AND (v.marca LIKE '%$nome_safe%' OR v.modello LIKE '%$nome_safe%')";
}

if (!empty($prezzo_min)) {
    $sql .= " AND v.prezzoVendita >= " . (float)$prezzo_min;
}

if (!empty($prezzo_max)) {
    $sql .= " AND v.prezzoVendita <= " . (float)$prezzo_max;
}

if ($solo_disponibili) {
    $sql .= " AND v.stato = 'Disponibile'";
}

// Gestione ordinamento
switch ($ordinamento) {
    case 'prezzo_asc': $sql .= " ORDER BY v.prezzoVendita ASC"; break;
    case 'prezzo_desc': $sql .= " ORDER BY v.prezzoVendita DESC"; break;
    default: $sql .= " ORDER BY v.marca ASC"; break;
}

$risultato = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo Veicoli - EasyDrive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="catologo.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <button id="toggleFilters" class="btn d-md-none mb-3">
                🔍 Mostra Filtri
            </button>

            <aside id="sidebarFiltri" class="col-md-3">
                <div class="filter-box">
                    <h4>Filtra Ricerca</h4>
                    <form method="GET" action="catologo.php">
                        <div class="mb-3">
                            <label>Cerca Modello/Marca</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($filtro_nome); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label>Prezzo Min (€)</label>
                            <input type="number" name="min" class="form-control" placeholder="0" value="<?php echo $prezzo_min; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Prezzo Max (€)</label>
                            <input type="number" name="max" class="form-control" placeholder="100000" value="<?php echo $prezzo_max; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Ordinamento</label>
                            <select name="ordine" class="form-select">
                                <option value="DEFAULT">A-Z</option>
                                <option value="prezzo_asc" <?php if($ordinamento == 'prezzo_asc') echo 'selected'; ?>>Prezzo Crescente</option>
                                <option value="prezzo_desc" <?php if($ordinamento == 'prezzo_desc') echo 'selected'; ?>>Prezzo Decrescente</option>
                            </select>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="disponibile" id="disponibile" <?php if($solo_disponibili) echo 'checked'; ?>>
                            <label class="form-check-label" for="disponibile">Solo Disponibili</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Applica Filtri</button>
                        <a href="catologo.php" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </form>
                </div>
            </aside>

            <main class="col-md-9">
                <div class="row">
                    <?php if ($risultato && $risultato->num_rows > 0): ?>
                        <?php while($veicolo = $risultato->fetch_assoc()): 
                            $is_disponibile = ($veicolo['stato'] == 'Disponibile');
                            $img_url = $veicolo['url_immagine'] ?: 'images/placeholder-car.png';
                        ?>
                            <div class="col-sm-6 col-lg-4 mb-4">
                                <div class="vehicle-card <?php echo !$is_disponibile ? 'not-available' : ''; ?>">
                                    <div class="img-container">
                                        <img src="<?php echo $img_url; ?>" alt="<?php echo $veicolo['modello']; ?>">
                                    </div>
                                    <div class="card-content">
                                        <h5><?php echo $veicolo['marca'] . " " . $veicolo['modello']; ?></h5>
                                        <p class="price">€ <?php echo number_format($veicolo['prezzoVendita'], 2, ',', '.'); ?></p>
                                        
                                        <div class="status-badge <?php echo $is_disponibile ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $veicolo['stato']; ?>
                                        </div>

                                        <div class="actions mt-3">
                                            <a href="dettaglio_veicolo.php?id=<?php echo $veicolo['telaio']; ?>" class="btn btn-detail">Dettagli</a>
                                            
                                            <?php if ($is_disponibile): ?>
                                                <a href="aggiungi_al_carrello.php?id=<?php echo $veicolo['telaio']; ?>" class="btn btn-cart">Aggiungi</a>
                                            <?php else: ?>
                                                <button class="btn btn-cart grayed-out" disabled>Non disp.</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <h3>Nessun veicolo trovato con questi criteri.</h3>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="header.js"></script>
    <script>
        // Logica per mostrare/nascondere i filtri su mobile
        document.getElementById('toggleFilters').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebarFiltri');
            sidebar.classList.toggle('active');
            this.textContent = sidebar.classList.contains('active') ? '✖ Chiudi Filtri' : '🔍 Mostra Filtri';
        });
    </script>
</body>
</html>