<?php
namespace it\unisa\easydrive\payment;

class PaymentLogic {
    public function validaDatiCarta($dati) {
        // Simulazione validazione business (es. non accettiamo carte scadute)
        if (strlen($dati['numero_carta']) !== 16) return false;
        if (strlen($dati['cvv']) !== 3) return false;
        

        return true;
    }
}