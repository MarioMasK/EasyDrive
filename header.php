<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connessione.php';

$queryIcons = "SELECT nome_icona, url_icona FROM icone";
$resultIcons = $conn->query($queryIcons);
$icone = [];

if ($resultIcons && $resultIcons->num_rows > 0) {
    while ($row = $resultIcons->fetch_assoc()) {
        $icone[$row['nome_icona']] = str_replace('dl=0', 'raw=1', $row['url_icona']);
    }
}

$default_img = "https://placehold.co/50";
?>

<header class="main-header">
    <nav class="main-nav">
        <a href="index.php" class="logo-mobile">
            <img src="images/logo.jpeg" alt="EasyDrive Logo">
        </a>
        <button id="hamburgerBtn" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <ul>
            <li class="nav-item far-left">
                <a href="carrello.php" class="icon-link">
                    <img src="<?php echo $icone['carrello'] ?? $default_img; ?>" alt="Carrello">
                    <span>Carrello</span>
                </a>
            </li>

            <li class="nav-item left">
                <a href="catologo.php" class="icon-link">
                    <img src="<?php echo $icone['catalogo'] ?? $default_img; ?>" alt="Catalogo">
                    <span>Catalogo</span>
                </a>
            </li>

            <li class="nav-item logo-desktop-item">
                <a href="index.php">
                    <img src="images/logo.jpeg" alt="EasyDrive Logo" class="main-logo">
                </a>
            </li>

            <?php if (isset($_SESSION['username'])): ?>
                
                <li class="nav-item right">
                    <a href="storico_operazioni.php" class="icon-link">
                        <img src="<?php echo $icone['storico'] ?? $default_img; ?>" alt="Storico">
                        <span>Storico</span>
                    </a>
                </li>

                <li class="nav-item nav-spacing">
                    <a href="profilo.php" class="icon-link">
                        <img src="<?php echo $icone['utente'] ?? $default_img; ?>" alt="Profilo">
                        <span>Ciao, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Utente'); ?></span>
                    </a>
                </li>

                <li class="nav-item far-right">
                    <a href="logout.php" class="btn-registrazione btn-logout">
                        Logout
                    </a>
                </li>

            <?php else: ?>

                <li class="nav-item right">
                    <a href="login.php" class="icon-link">
                        <img src="<?php echo $icone['utente'] ?? $default_img; ?>" alt="Login">
                        <span>Login</span>
                    </a>
                </li>
                <li class="nav-item far-right">
                    <a href="registrazione.php" class="btn-registrazione">
                        Registrazione
                    </a>
                </li>

            <?php endif; ?>
        </ul>
    </nav>
</header>