<?php
require_once 'connessione.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username_loggato = $_SESSION['username'];

$sql = "SELECT Tipo, Data, Importo, marca, modello 
        FROM StoricoOperazioni 
        WHERE Cliente = '$username_loggato' 
        ORDER BY Data DESC";

$risultato = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Storico Ordini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="header.css">
</head>
<body class="bg-light">

    <?php include 'header.php'; ?>

    <main class="container my-5" style="min-height: 70vh;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-clock-history fs-2 text-primary me-3" style="color: #20B2AA !important;"></i>
                    <h2 class="fw-bold mb-0">Il tuo Storico Operazioni</h2>
                </div>

                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4 py-3">Operazione e Veicolo</th>
                                    <th class="py-3">Data</th>
                                    <th class="py-3">Importo</th>
                                    <th class="py-3 text-center">Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($risultato && $risultato->num_rows > 0): ?>
                                    <?php while($row = $risultato->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 py-3 align-middle">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark">
                                                        <?php echo htmlspecialchars($row['marca'] . " " . $row['modello']); ?>
                                                    </span>
                                                    <div>
                                                        <?php if ($row['Tipo'] == 'Vendita'): ?>
                                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 small">
                                                                <i class="bi bi-cart-check me-1"></i> Vendita
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-info-subtle text-info border border-info px-2 py-1 small">
                                                                <i class="bi bi-calendar-range me-1"></i> Noleggio
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 align-middle">
                                                <span class="text-secondary small">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($row['Data'])); ?>
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle fw-bold text-orange-easy" style="color: #FF8C00;">
                                                € <?php echo number_format($row['Importo'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <span class="text-success small fw-bold">Completato</span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                                            <p class="text-muted">Non hai ancora effettuato nessuna operazione con EasyDrive.</p>
                                            <a href="catologo.php" class="btn btn-outline-primary btn-sm rounded-pill px-4">Esplora il catalogo</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
