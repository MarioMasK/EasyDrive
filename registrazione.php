<?php
require_once 'connessione.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$messaggio_errore = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupero dati
    $username     = trim($conn->real_escape_string($_POST['username']));
    $email        = trim($conn->real_escape_string($_POST['email']));
    $password_raw = $_POST['password'];
    $nome         = trim($conn->real_escape_string($_POST['nome']));
    $cognome      = trim($conn->real_escape_string($_POST['cognome']));
    $sesso        = $_POST['sesso']; // Valori: M, F, Altro
    $telefono     = trim($conn->real_escape_string($_POST['telefono']));
    $dataNascita  = $_POST['data_nascita'];
    $citta        = trim($conn->real_escape_string($_POST['citta']));
    $provincia    = strtoupper(trim($conn->real_escape_string($_POST['provincia'])));
    $via          = trim($conn->real_escape_string($_POST['via']));
    $civico       = trim($conn->real_escape_string($_POST['civico']));
    $cap          = trim($conn->real_escape_string($_POST['cap']));

    // --- 1. CONTROLLO CAMPI VUOTI ---
    if (empty($username) || empty($email) || empty($password_raw) || empty($nome) || empty($cognome) || empty($cap) || empty($dataNascita)) {
        $messaggio_errore = "Per favore, compila tutti i campi obbligatori.";
    } 
    // --- 2. VALIDAZIONE PASSWORD ---
    elseif (strlen($password_raw) < 8 || !preg_match('/[A-Z]/', $password_raw) || !preg_match('/[0-9]/', $password_raw)) {
        $messaggio_errore = "La password deve avere almeno 8 caratteri, una maiuscola e un numero.";
    }
    // --- 3. VALIDAZIONE CAP (Esattamente 5 numeri) ---
    elseif (!preg_match('/^[0-9]{5}$/', $cap)) {
        $messaggio_errore = "Il CAP deve essere composto da esattamente 5 cifre numeriche.";
    }
    // --- 4. VALIDAZIONE DATA DI NASCITA (Non prima del 01/01/1926) ---
    elseif ($dataNascita < "1926-01-01") {
        $messaggio_errore = "La data di nascita non può essere precedente al 01/01/1926.";
    }
    else {
        // --- 5. CONTROLLO UNICITÀ USERNAME/EMAIL ---
        $checkQuery = "SELECT username FROM Account WHERE username = '$username' OR email = '$email'";
        $risultatoCheck = $conn->query($checkQuery);

        if ($risultatoCheck && $risultatoCheck->num_rows > 0) {
            $messaggio_errore = "Username o Email già utilizzati da un altro account.";
        } else {
            // --- 6. INSERIMENTO NEL DATABASE ---
            $hashedPassword = password_hash($password_raw, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO Account (
                        username, hashedPassword, nome, cognome, sesso, 
                        email, numeroTelefono, dataNascita, provincia, 
                        citta, via, numeroCivico, cap, ruolo
                    ) VALUES (
                        '$username', '$hashedPassword', '$nome', '$cognome', '$sesso', 
                        '$email', '$telefono', '$dataNascita', '$provincia', 
                        '$citta', '$via', '$civico', '$cap', 'cliente_registrato'
                    )";
            
            if ($conn->query($sql)) {
                $_SESSION['username'] = $username;
                $_SESSION['nome'] = $nome;
                $_SESSION['messaggio_successo'] = "Registrazione avvenuta con successo!";
                header("Location: index.php");
                exit(); 
            } else {
                $messaggio_errore = "Errore durante la registrazione: " . $conn->error;
            }
        }
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
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="registrazione.css">
</head>
<body class="bg-light">

    <?php include 'header.php'; ?>

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

                            <?php if ($messaggio_errore): ?>
                                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div><?php echo $messaggio_errore; ?></div>
                                </div>
                            <?php endif; ?>

                            <form action="registrazione.php" method="POST" class="row g-3">
                                <div class="col-12"><h6 class="text-aqua-marine fw-bold text-uppercase border-bottom pb-2">Credenziali Accesso</h6></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="es. Mario88" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="mario@esempio.it" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimo 8 caratteri, 1 maiuscola e 1 numero" required>
                                </div>

                                <div class="col-12 mt-4"><h6 class="text-aqua-marine fw-bold text-uppercase border-bottom pb-2">Dati Anagrafici</h6></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nome</label>
                                    <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Cognome</label>
                                    <input type="text" name="cognome" class="form-control" value="<?php echo htmlspecialchars($_POST['cognome'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Sesso</label>
                                    <select name="sesso" class="form-select">
                                        <option value="M">Uomo</option>
                                        <option value="F">Donna</option>
                                        <option value="Altro">Altro / Non specificato</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Data di Nascita</label>
                                    <input type="date" name="data_nascita" class="form-control" min="1926-01-01" value="<?php echo $_POST['data_nascita'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Telefono</label>
                                    <input type="tel" name="telefono" class="form-control" placeholder="es. 3331234567" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                                </div>

                                <div class="col-12 mt-4"><h6 class="text-aqua-marine fw-bold text-uppercase border-bottom pb-2">Residenza</h6></div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Indirizzo (Via/Piazza)</label>
                                    <input type="text" name="via" class="form-control" value="<?php echo htmlspecialchars($_POST['via'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Civico</label>
                                    <input type="text" name="civico" class="form-control" value="<?php echo htmlspecialchars($_POST['civico'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">CAP</label>
                                    <input type="text" name="cap" class="form-control" maxlength="5" placeholder="es. 84100" value="<?php echo htmlspecialchars($_POST['cap'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Città</label>
                                    <input type="text" name="citta" class="form-control" value="<?php echo htmlspecialchars($_POST['citta'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Provincia (Sigla)</label>
                                    <input type="text" name="provincia" class="form-control" maxlength="2" placeholder="es. SA" value="<?php echo htmlspecialchars($_POST['provincia'] ?? ''); ?>" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="background-color: #20B2AA; border: none;">
                                        REGISTRATI ORA
                                    </button>
                                </div>
                                <div class="text-center mt-3">
                                    <p class="small text-muted">Hai già un account? <a href="login.php" class="text-aqua-marine fw-bold">Accedi qui</a></p>
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