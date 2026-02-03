<?php
// 1. GESTIONE DELLA SESSIONE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. LOGICA DEL PREFISSO DINAMICO
// Se il file logo.jpeg esiste qui, siamo nella Root. Altrimenti siamo in una sottocartella.
$prefix = file_exists('images/logo.jpeg') ? "" : "../";

// 3. INCLUSIONE COMPONENTI (Usiamo __DIR__ per i file PHP, che non sbaglia mai)
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/CoreDAO.php'; 
require_once __DIR__ . '/core/CoreLogic.php';

// Inizializzazione logica icone
$coreLogic = new \it\unisa\easydrive\core\CoreLogic();
$icone = $coreLogic->getFormattedIcons();

$default_img = "https://placehold.co/50";
?>

<header class="main-header">
    <nav class="main-nav">
        <a href="<?php echo $prefix; ?>index.php" class="logo-mobile">
            <img src="<?php echo $prefix; ?>images/logo.jpeg" alt="EasyDrive Logo">
        </a>
        
        <button id="hamburgerBtn" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <ul>
            <li class="nav-item far-left">
                <a href="<?php echo $prefix; ?>Sales/carrello.php" class="icon-link">
                    <img src="<?php echo $icone['carrello'] ?? $default_img; ?>" alt="Carrello">
                    <span>Carrello</span>
                </a>
            </li>

            <li class="nav-item left">
                <a href="<?php echo $prefix; ?>Catalog/catalogo.php" class="icon-link">
                    <img src="<?php echo $icone['catalogo'] ?? $default_img; ?>" alt="Catalogo">
                    <span>Catalogo</span>
                </a>
            </li>

            <li class="nav-item logo-desktop-item">
                <a href="<?php echo $prefix; ?>index.php">
                    <img src="<?php echo $prefix; ?>images/logo.jpeg" alt="EasyDrive Logo" class="main-logo">
                </a>
            </li>

            <?php if (isset($_SESSION['username'])): ?>
                
                <li class="nav-item right">
                    <a href="<?php echo $prefix; ?>Sales/storico_operazioni.php" class="icon-link">
                        <img src="<?php echo $icone['storico'] ?? $default_img; ?>" alt="Storico">
                        <span>Storico</span>
                    </a>
                </li>

                <li class="nav-item nav-spacing">
                    <a href="<?php echo $prefix; ?>Account/profilo.php" class="icon-link">
                        <img src="<?php echo $icone['utente'] ?? $default_img; ?>" alt="Profilo">
                        <span>Ciao, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Utente'); ?></span>
                    </a>
                </li>

                <li class="nav-item far-right">
                    <a href="<?php echo $prefix; ?>Account/logout.php" class="btn-registrazione btn-logout">
                        Logout
                    </a>
                </li>

            <?php else: ?>

                <li class="nav-item right">
                    <a href="<?php echo $prefix; ?>Account/login.php" class="icon-link">
                        <img src="<?php echo $icone['utente'] ?? $default_img; ?>" alt="Login">
                        <span>Login</span>
                    </a>
                </li>
                
                <li class="nav-item far-right">
                    <a href="<?php echo $prefix; ?>Account/registrazione.php" class="btn-registrazione">
                        Registrazione
                    </a>
                </li>

            <?php endif; ?>
        </ul>
    </nav>
</header>