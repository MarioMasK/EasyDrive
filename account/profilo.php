<?php
require_once __DIR__ . '/../core/Database.php';
require_once 'AccountDAO.php';
require_once 'AccountLogic.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Controllo Accesso
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$accountLogic = new \it\unisa\easydrive\account\AccountLogic();
$user = $accountLogic->ottieniProfilo($_SESSION['username']);

// Se l'utente non viene trovato (es. rimosso mentre era loggato)
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Il Mio Profilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../profilo.css">
</head>
<body class="bg-light">

    <?php include '../header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="profile-header shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between bg-white">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-4" style="width: 80px; height: 80px; background: #20B2AA; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold;">
                            <?php echo strtoupper(substr($user['nome'], 0, 1) . substr($user['cognome'], 0, 1)); ?>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($user['nome'] . " " . $user['cognome']); ?></h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-info text-dark"><?php echo $user['ruolo_formattato']; ?></span>
                                <span class="ms-2 small">Membro dal: <?php echo date('d/m/Y', strtotime($user['dataCreazione'])); ?></span>
                            </p>
                        </div>
                    </div>
                    <a href="../sales/storico_operazioni.php" class="btn btn-outline-dark rounded-pill">
                        <i class="bi bi-clock-history me-1"></i> I miei ordini
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary">Informazioni Personali</h5>
                                <div class="mb-3">
                                    <label class="small text-muted d-block">Email</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted d-block">Telefono</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['numeroTelefono'] ?: 'Non inserito'); ?></span>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="small text-muted d-block">Data di Nascita</label>
                                        <span class="fw-bold"><?php echo date('d/m/Y', strtotime($user['dataNascita'])); ?></span>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted d-block">Sesso</label>
                                        <span class="fw-bold"><?php echo htmlspecialchars($user['sesso']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2 text-warning">Residenza</h5>
                                <div class="mb-3">
                                    <label class="small text-muted d-block">Indirizzo</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['via'] . ", " . $user['numeroCivico']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted d-block">Città e Provincia</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['citta'] . " (" . $user['provincia'] . ")"); ?></span>
                                </div>
                                <div>
                                    <label class="small text-muted d-block">CAP</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['cap']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-white rounded-4 shadow-sm border-start border-4 border-info">
                    <p class="mb-0 text-muted">
                        Hai bisogno di assistenza? Contattaci: <strong id="phoneNum">0894563294</strong>
                        <button class="btn btn-sm" onclick="copyNumber()"><i class="bi bi-clipboard" id="copyIcon"></i></button>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php include '../footer.php'; ?>
    <script>
    function copyNumber() {
        const num = document.getElementById('phoneNum').innerText;
        navigator.clipboard.writeText(num);
        alert("Numero copiato!");
    }
    </script>
</body>
</html>