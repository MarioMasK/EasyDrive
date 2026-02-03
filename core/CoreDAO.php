<?php
namespace it\unisa\easydrive\core;

class CoreDAO {
    public function getAllIcons() {
        $db = Database::getConnection();
        $query = "SELECT nome_icona, url_icona FROM icone";
        return $db->query($query);
    }
}