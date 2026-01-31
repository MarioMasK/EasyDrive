<?php
require_once 'connessione.php';

// Avvia la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CONTROLLO ACCESSO: Solo utenti loggati
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username_sessione = $_SESSION['username'];

// 2. RECUPERO DATI DAL DATABASE
$stmt = $conn->prepare("SELECT * FROM Account WHERE username = ?");
$stmt->bind_param("s", $username_sessione);
$stmt->execute();
$risultato = $stmt->get_result();

if ($risultato && $risultato->num_rows > 0) {
    $user = $risultato->fetch_assoc();
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Il Mio Profilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="profilo.css">
</head>
<body class="bg-light">

    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="profile-header shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-4">
                            <?php echo strtoupper(substr($user['nome'], 0, 1) . substr($user['cognome'], 0, 1)); ?>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1"><?php echo htmlspecialchars($user['nome'] . " " . $user['cognome']); ?></h2>
                            <p class="text-muted mb-0">
                                <span class="badge bg-aqua-marine"><?php echo str_replace('_', ' ', $user['ruolo']); ?></span>
                                <span class="ms-2">Membro dal: <?php echo date('d/m/Y', strtotime($user['dataCreazione'])); ?></span>
                            </p>
                        </div>
                    </div>
                    <a href="storico_operazioni.php" class="btn btn-outline-dark rounded-pill">
                        <i class="bi bi-clock-history me-1"></i> I miei ordini
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">
                                    <i class="bi bi-person-lines-fill me-2 text-aqua-marine"></i>Informazioni Personali
                                </h5>
                                
                                <div class="info-group mb-3">
                                    <label class="small text-muted d-block">Username</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></span>
                                </div>
                                
                                <div class="info-group mb-3">
                                    <label class="small text-muted d-block">Email</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="small text-muted d-block">Telefono</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['numeroTelefono'] ?: 'Non inserito'); ?></span>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="small text-muted d-block">Data di Nascita</label>
                                        <span class="fw-bold"><?php echo date('d/m/Y', strtotime($user['dataNascita'])); ?></span>
                                    </div>
                                    <div class="col-6 mb-3">
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
                                <h5 class="fw-bold mb-4 border-bottom pb-2">
                                    <i class="bi bi-geo-alt-fill me-2 text-orange-easy"></i>Indirizzo di Residenza
                                </h5>
                                
                                <div class="info-group mb-3">
                                    <label class="small text-muted d-block">Via / Indirizzo</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['via'] . ", " . $user['numeroCivico']); ?></span>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="small text-muted d-block">Città</label>
                                        <span class="fw-bold"><?php echo htmlspecialchars($user['citta']); ?></span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="small text-muted d-block">Provincia / CAP</label>
                                        <span class="fw-bold"><?php echo htmlspecialchars($user['provincia'] . " (" . $user['cap'] . ")"); ?></span>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="small text-muted d-block">Nazione</label>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user['nazione']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="assistance-box p-4 mb-4 rounded-4 shadow-sm">
                        <p class="text-muted mb-0">
                            Hai bisogno di assistenza? Contattaci al numero 
                            <span class="contact-highlight" id="phoneNum">0894563294</span>
                            <button class="btn btn-sm btn-copy" onclick="copyNumber()" title="Copia Numero">
                                <i class="bi bi-clipboard" id="copyIcon"></i>
                            </button>
                            <br>oppure tramite mail all'indirizzo <a href="mailto:info@easydrive.it" class="text-aqua-marine fw-bold text-decoration-none"><em>info@easydrive.it</em></a>
                        </p>
                </div>

            </div>
        </div>
    </main>

    <?php include 'footer.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="header.js"></script>
    <script>
    function copyNumber() {
    const num = document.getElementById('phoneNum').innerText;
    const icon = document.getElementById('copyIcon');
    
    navigator.clipboard.writeText(num).then(() => {
        // Feedback visivo: cambia l'icona in un check
        icon.classList.replace('bi-clipboard', 'bi-check-lg');
        icon.classList.add('text-success');
        
        setTimeout(() => {
            // Ripristina l'icona originale dopo 2 secondi
            icon.classList.replace('bi-check-lg', 'bi-clipboard');
            icon.classList.remove('text-success');
        }, 2000);
    });
    }
    </script>
</body>
</html>