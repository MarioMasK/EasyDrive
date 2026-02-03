<?php
// 1. LOGICA DEL PREFISSO BASATA SUL CSS
// Se header.css esiste qui, siamo nella root. Altrimenti torniamo indietro.
$prefix = file_exists('header.css') ? "" : "../";
?>

<link rel="stylesheet" href="<?php echo $prefix; ?>footer.css">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">

<footer class="footer-custom">
    <div class="footer-container">
        
        <div class="footer-section">
            <h2 class="footer-logo">EasyDrive</h2>
            <p>
                EasyDrive è la soluzione innovativa nata dal Progetto di Ingegneria 2026. 
                Semplifichiamo la gestione della mobilità urbana attraverso algoritmi avanzati.
            </p>
        </div>

        <div class="footer-section">
            <h3>Esplora</h3>
            <ul>
                <li><a href="<?php echo $prefix; ?>index.php">Home</a></li>
                <li><a href="<?php echo $prefix; ?>Catalog/catalogo.php">Catalogo</a></li>
                <li><a href="#">Documentazione</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Seguici</h3>
            <div class="social-icons">
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="FB"></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="IG"></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/3256/3256013.png" alt="IN"></a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 EasyDrive - Progetto Ingegneria</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>