<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'SalesDAO.php';
require_once 'SalesLogic.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Controllo Accesso
if (!isset($_SESSION['username'])) {
    header("Location: ../account/login.php");
    exit();
}

$salesLogic = new \it\unisa\easydrive\sales\SalesLogic();
$risultato = $salesLogic->ottieniStoricoUtente($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Storico Operazioni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
    <style>
        .text-orange-easy { color: #FF8C00; }
        .bg-aqua-easy { background-color: #20B2AA; }
        .table-dark { background-color: #000000; }
    </style>
</head>
<body class="bg-light">

    <?php include '../header.php'; ?>

    <main class="container my-5" style="min-height: 70vh;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-clock-history fs-2 me-3" style="color: #20B2AA;"></i>
                    <h2 class="fw-bold mb-0">Il tuo Storico Operazioni</h2>
                </div>

                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-4 py-3">Operazione e Veicolo</th>
                                    <th class="py-3">Data</th>
                                    <th class="py-3 text-end">Importo</th>
                                    <th class="py-3 text-center">Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($risultato && $risultato->num_rows > 0): ?>
                                    <?php while($row = $risultato->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 py-3 align-middle">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark fs-5">
                                                        <?php echo htmlspecialchars($row['marca'] . " " . $row['modello']); ?>
                                                    </span>
                                                    <div class="mt-1">
                                                        <?php if ($row['Tipo'] == 'Vendita'): ?>
                                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                                <i class="bi bi-cart-check me-1"></i> Acquisto
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-info-subtle text-info border border-info px-2 py-1">
                                                                <i class="bi bi-calendar-range me-1"></i> Noleggio
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-3 align-middle">
                                                <span class="text-secondary">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php echo date('d/m/Y', strtotime($row['Data'])); ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo date('H:i', strtotime($row['Data'])); ?></small>
                                                </span>
                                            </td>

                                            <td class="py-3 align-middle text-end fw-bold fs-5">
                                                <span class="text-orange-easy">
                                                    € <?php echo number_format($row['Importo'], 2, ',', '.'); ?>
                                                </span>
                                            </td>

                                            <td class="py-3 align-middle text-center">
                                                <span class="badge rounded-pill bg-success px-3">Completato</span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                                            <p class="text-muted fs-5">Nessuna operazione trovata.</p>
                                            <a href="../catalog/catalogo.php" class="btn btn-primary rounded-pill px-4" style="background-color: #20B2AA; border: none;">Sfoglia Catalogo</a>
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

    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>