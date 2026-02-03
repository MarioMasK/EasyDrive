<?php
namespace it\unisa\easydrive\core;

class CoreLogic {
    private $dao;

    public function __construct() {
        $this->dao = new CoreDAO();
    }

    public function getFormattedIcons() {
        $result = $this->dao->getAllIcons();
        $icone = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Logica di formattazione URL
                $icone[$row['nome_icona']] = str_replace('dl=0', 'raw=1', $row['url_icona']);
            }
        }
        return $icone;
    }
}