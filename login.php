<?php
require_once 'connessione.php';

// Controlliamo se la sessione è già partita
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$messaggio_errore = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim per rimuovere spazi accidentali
    $identificativo = trim($_POST['identificativo']); 
    $password_inserita = $_POST['password'];

    if (empty($identificativo) || empty($password_inserita)) {
        $messaggio_errore = "Per favore, compila tutti i campi.";
    } else {
        // Utilizzo di Prepared Statement per massima sicurezza
        $stmt = $conn->prepare("SELECT AccountId, username, nome, ruolo, hashedPassword FROM Account WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $identificativo, $identificativo);
        $stmt->execute();
        $risultato = $stmt->get_result();

        if ($risultato && $risultato->num_rows > 0) {
            $utente = $risultato->fetch_assoc();
            
            // Verifica della password
            if (password_verify($password_inserita, $utente['hashedPassword'])) {
                // Rigenera ID sessione per sicurezza
                session_regenerate_id(true);

                $_SESSION['utente_id'] = $utente['AccountId'];
                $_SESSION['username'] = $utente['username'];
                $_SESSION['nome'] = $utente['nome'];
                $_SESSION['ruolo'] = $utente['ruolo'];

                // --- LOGICA DI SINCRONIZZAZIONE CARRELLO ---
                
                $user = $utente['username'];
                
                // Inizializziamo il carrello in sessione se non esiste
                if (!isset($_SESSION['carrello'])) {
                    $_SESSION['carrello'] = array();
                }

                // 1. Recuperiamo i veicoli salvati nel DB per questo utente
                $stmt_wish = $conn->prepare("SELECT telaio FROM Wishlist_Interesse WHERE username = ?");
                $stmt_wish->bind_param("s", $user);
                $stmt_wish->execute();
                $res_wish = $stmt_wish->get_result();

                while ($row_wish = $res_wish->fetch_assoc()) {
                    $t_db = $row_wish['telaio'];
                    
                    // 2. CONTROLLO DISPONIBILITÀ
                    $stmt_disp = $conn->prepare("SELECT stato FROM Veicolo WHERE telaio = ? AND stato = 'Disponibile'");
                    $stmt_disp->bind_param("s", $t_db);
                    $stmt_disp->execute();
                    $res_disp = $stmt_disp->get_result();
                    
                    if ($res_disp && $res_disp->num_rows > 0) {
                        // Se disponibile e non è già in sessione, lo aggiungiamo
                        if (!in_array($t_db, $_SESSION['carrello'])) {
                            $_SESSION['carrello'][] = $t_db;
                        }
                    } else {
                        // Se NON è più disponibile, puliamo il DB automaticamente
                        $stmt_del = $conn->prepare("DELETE FROM Wishlist_Interesse WHERE username = ? AND telaio = ?");
                        $stmt_del->bind_param("ss", $user, $t_db);
                        $stmt_del->execute();
                        $stmt_del->close();
                    }
                    $stmt_disp->close();
                }
                $stmt_wish->close();

                
                
                $_SESSION['messaggio_successo'] = "Bentornato, " . htmlspecialchars($utente['nome']) . "!";
                
                header("Location: index.php");
                exit();
            } else {
                $messaggio_errore = "Password non corretta. Riprova.";
            }
        } else {
            $messaggio_errore = "Nessun account trovato con queste credenziali.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Accedi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="login.css">
</head>
<body class="bg-light">

    <?php include 'header.php'; ?>

    <div class="container login-container my-5">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card card-login shadow-lg border-0 overflow-hidden" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-lg-5 login-sidebar d-none d-lg-flex flex-column justify-content-center align-items-center text-white p-4" style="background: linear-gradient(135deg, #20B2AA 0%, #000 100%);">
                            <i class="bi bi-person-check-fill display-1 mb-3"></i>
                            <h2 class="fw-bold">Area Clienti</h2>
                            <p class="small text-center opacity-75">Accedi per gestire i tuoi ordini e scoprire le novità della flotta 2026.</p>
                        </div>
                        
                        <div class="col-lg-7 p-4 p-md-5 bg-white">
                            <h3 class="fw-bold mb-4 text-dark">Login</h3>

                            <?php if ($messaggio_errore): ?>
                                <div class="alert alert-danger d-flex align-items-center mb-4 py-2 border-0 shadow-sm" style="background-color: #fff5f5; color: #c53030;">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div class="small fw-bold"><?php echo $messaggio_errore; ?></div>
                                </div>
                            <?php endif; ?>

                            <form action="login.php" method="POST">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary">Username o Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="identificativo" class="form-control bg-light border-start-0" placeholder="Username o Email" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-login w-100 mb-3 py-2 fw-bold shadow-sm" style="background-color: #FF8C00; color: white;">
                                    Accedi <i class="bi bi-arrow-right-circle ms-2"></i>
                                </button>

                                <div class="text-center mt-4">
                                    <p class="small text-muted">Non sei ancora dei nostri? <br>
                                        <a href="registrazione.php" class="text-decoration-none fw-bold" style="color: #20B2AA;">Crea un account ora</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>