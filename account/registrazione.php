<?php
// 1. INCLUSIONI E LOGICA DI TESTA
require_once __DIR__ . '/../core/Database.php';
require_once 'AccountDAO.php';
require_once 'AccountLogic.php';

// Avviamo la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$accountLogic = new \it\unisa\easydrive\account\AccountLogic();

/**
 * LOGICA DI RECUPERO MESSAGGI (FLASH MESSAGES)
 * Recuperiamo l'errore dalla sessione, lo salviamo in una variabile locale per la vista
 * e poi lo cancelliamo dalla sessione così non riappare al prossimo refresh.
 */
$messaggio_errore = $_SESSION['errore_registrazione'] ?? "";
unset($_SESSION['errore_registrazione']); 

// 2. GESTIONE DELL'INVIO DEL FORM (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password_inserita = $_POST['password'] ?? '';
    
    $risultato = $accountLogic->validaEInregistra($_POST, $password_inserita);
    
    if ($risultato === true) {
        // SUCCESS-FLOW
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['nome'] = $_POST['nome'];
        $_SESSION['messaggio_successo'] = "Registrazione avvenuta con successo!";
        header("Location: ../index.php");
        exit();
    } else {
        // ERROR-FLOW: Salviamo l'errore in sessione e ricarichiamo la pagina
        $_SESSION['errore_registrazione'] = $risultato;
        header("Location: registrazione.php");
        exit(); 
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyDrive - Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../registrazione.css">
    <style>
        .text-aqua-marine { color: #20B2AA; }
        .btn-aqua { background-color: #20B2AA; border: none; color: white; transition: 0.3s; }
        .btn-aqua:hover { background-color: #1a938c; transform: translateY(-2px); color: white; }
        .alert { animation: slideIn 0.5s ease-out; border-radius: 12px; }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-light">

    <?php include '../header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-4 d-none d-lg-flex flex-column justify-content-center align-items-center text-white p-5" 
                             style="background: linear-gradient(135deg, #20B2AA 0%, #000 100%);">
                            <i class="bi bi-person-plus display-1 mb-4"></i>
                            <h2 class="fw-bold">EasyDrive</h2>
                            <p class="text-center opacity-75">Crea un account per gestire i tuoi noleggi e acquisti in totale sicurezza.</p>
                        </div>

                        <div class="col-lg-8 p-4 p-md-5 bg-white">
                            <h3 class="fw-bold mb-4">Crea il tuo profilo</h3>

                            <?php if (!empty($messaggio_errore)): ?>
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                    <div>
                                        <strong>Attenzione!</strong><br>
                                        <?php echo htmlspecialchars($messaggio_errore); ?>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form action="registrazione.php" method="POST" class="row g-3" novalidate>
                                <div class="col-12"><h6 class="text-aqua-marine fw-bold text-uppercase border-bottom pb-2">Credenziali Accesso</h6></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimo 8 caratteri, 1 maiuscola e 1 numero">
                                </div>

                                <div class="col-12 mt-4"><h6 class="text-aqua-marine fw-bold text-uppercase border-bottom pb-2">Dati Anagrafici</h6></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nome</label>
                                    <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Cognome</label>
                                    <input type="text" name="cognome" class="form-control" value="<?php echo htmlspecialchars($_POST['cognome'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Sesso</label>
                                    <select name="sesso" class="form-select">
                                        <option value="M" <?php echo (($_POST['sesso'] ?? '') == 'M') ? 'selected' : ''; ?>>Uomo</option>
                                        <option value="F" <?php echo (($_POST['sesso'] ?? '') == 'F') ? 'selected' : ''; ?>>Donna</option>
                                        <option value="Altro" <?php echo (($_POST['sesso'] ?? '') == 'Altro') ? 'selected' : ''; ?>>Altro</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Data di Nascita</label>
                                    <input type="date" name="data_nascita" class="form-control" value="<?php echo $_POST['data_nascita'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Telefono</label>
                                    <input type="tel" name="telefono" class="form-control" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                                </div>

                                <div class="col-12 mt-4"><h6 class="text-aqua-marine fw-bold text-uppercase border-bottom pb-2">Residenza</h6></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Indirizzo (Via/Piazza)</label>
                                    <input type="text" name="via" class="form-control" value="<?php echo htmlspecialchars($_POST['via'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Civico</label>
                                    <input type="text" name="civico" class="form-control" value="<?php echo htmlspecialchars($_POST['civico'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">CAP</label>
                                    <input type="text" name="cap" class="form-control" maxlength="5" value="<?php echo htmlspecialchars($_POST['cap'] ?? ''); ?>">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Città</label>
                                    <input type="text" name="citta" class="form-control" value="<?php echo htmlspecialchars($_POST['citta'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Provincia</label>
                                    <input type="text" name="provincia" class="form-control" maxlength="2" placeholder="es. SA" value="<?php echo htmlspecialchars($_POST['provincia'] ?? ''); ?>">
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-aqua w-100 py-3 fw-bold shadow">
                                        REGISTRATI ORA
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onload = function() {
            if (document.querySelector('.alert-danger')) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        };
    </script>
</body>
</html>