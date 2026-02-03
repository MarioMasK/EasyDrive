<?php
namespace it\unisa\easydrive\core;

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance == null) {
            $host = "localhost";
            $user = "ingegnere"; 
            $pass = "ingegnere"; 
            $db   = "easydrive_db";

            // Creazione connessione
            self::$instance = new \mysqli($host, $user, $pass, $db);

            // Controllo errori
            if (self::$instance->connect_error) {
                die("Errore critico di connessione (Core Tier): " . self::$instance->connect_error);
            }

            // Set charset
            self::$instance->set_charset("utf8mb4");
        }
        return self::$instance;
    }
}