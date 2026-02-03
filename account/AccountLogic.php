<?php
namespace it\unisa\easydrive\account;


require_once __DIR__ . '/AccountDAO.php';
class AccountLogic {
    private $dao;

    public function __construct() {
        $this->dao = new AccountDAO();
    }

    public function autentica($identificativo, $password) {
        $utente = $this->dao->getAccountByIdentifier($identificativo);
        if ($utente && password_verify($password, $utente['hashedPassword'])) {
            return $utente;
        }
        return false;
    }

    public function sincronizzaCarrello($username) {
        if (!isset($_SESSION['carrello'])) {
            $_SESSION['carrello'] = array();
        }

        $res_wish = $this->dao->getWishlistByUser($username);
        while ($row = $res_wish->fetch_assoc()) {
            $telaio = $row['telaio'];
            if ($this->dao->checkVeicoloDisponibile($telaio)) {
                if (!in_array($telaio, $_SESSION['carrello'])) {
                    $_SESSION['carrello'][] = $telaio;
                }
            } else {
                $this->dao->removeWishlistItem($username, $telaio);
            }
        }
    }
 public function validaEInregistra($dati, $password_raw) {
    // 1. ELENCO COMPLETO CAMPI OBBLIGATORI
    $campi_obbligatori = [
        'username' => 'Username', 'email' => 'Email', 'nome' => 'Nome', 
        'cognome' => 'Cognome', 'data_nascita' => 'Data di Nascita',
        'via' => 'Indirizzo', 'civico' => 'Civico', 'citta' => 'Città', 
        'provincia' => 'Provincia', 'cap' => 'CAP'
    ];

    $mancanti = [];
    foreach ($campi_obbligatori as $chiave => $nome_campo) {
        if (empty(trim($dati[$chiave] ?? ''))) {
            $mancanti[] = $nome_campo;
        }
    }
    if (empty($password_raw)) $mancanti[] = "Password";

    if (!empty($mancanti)) {
        return "Mancano i seguenti dati: " . implode(", ", $mancanti) . ".";
    }

    // 2. CONTROLLO DATA DI NASCITA (Solo che non sia nel futuro)
    $data_nascita = new \DateTime($dati['data_nascita']);
    $oggi = new \DateTime();
    if ($data_nascita > $oggi) {
        return "La data di nascita non può essere nel futuro.";
    }

    // 3. VALIDAZIONE RESIDENZA (Provincia 2 lettere, CAP 5 cifre)
    if (!preg_match('/^[A-Z]{2}$/i', $dati['provincia'])) {
        return "La provincia deve essere di 2 lettere (es: SA, NA).";
    }
    if (!preg_match('/^[0-9]{5}$/', $dati['cap'])) {
        return "Il CAP deve essere di esattamente 5 cifre.";
    }

    // 4. VALIDAZIONE PASSWORD
    if (strlen($password_raw) < 8 || !preg_match('/[A-Z]/', $password_raw) || !preg_match('/[0-9]/', $password_raw)) {
        return "La password deve avere almeno 8 caratteri, una maiuscola e un numero.";
    }

    // 5. CONTROLLO UNICITÀ E INVIO AL DAO
    if ($this->dao->esisteGia($dati['username'], $dati['email'])) {
        return "Username o Email già registrati.";
    }

    $dati['hashedPassword'] = password_hash($password_raw, PASSWORD_DEFAULT);
    
    // Inserimento finale
    if ($this->dao->inserisciAccount($dati)) {
        return true;
    }
    
    return "Errore tecnico durante il salvataggio.";
}
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Svuotiamo l'array $_SESSION
        $_SESSION = array();

        // 2. Cancelliamo il cookie di sessione
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Distruggiamo la sessione sul server
        session_destroy();
    }
    public function ottieniProfilo($username) {
        $user = $this->dao->getFullAccountByUsername($username);
        
        if (!$user) return null;

        // Piccola logica di business: pulizia del ruolo per la visualizzazione
        $user['ruolo_formattato'] = str_replace('_', ' ', $user['ruolo']);
        
        return $user;
    }
}
