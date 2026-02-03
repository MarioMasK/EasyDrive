<?php
namespace it\unisa\easydrive\catalog;

class CatalogLogic {
    private $dao;

    public function __construct() {
        $this->dao = new CatalogDAO();
    }

    // Servizio esposto per la Home Page
    public function getHomePageHighlights() {
        return $this->dao->getFeaturedVehicles(4);
    }

    public function ricercaVeicoli($params) {
        // Pulizia e default dei parametri
        $filters = [
            'nome' => $params['nome'] ?? '',
            'min'  => $params['min'] ?? '',
            'max'  => $params['max'] ?? '',
            'disponibile' => isset($params['disponibile']),
            'noleggio'    => isset($params['noleggio']),
            'ordine'      => $params['ordine'] ?? 'DEFAULT'
        ];

        return $this->dao->getFilteredVehicles($filters);
    }
    public function fornisceDettagliVeicolo($telaio) {
        $veicolo = $this->dao->getVeicoloByTelaio($telaio);
        
        if (!$veicolo) return null;

        // Trasformazione link Dropbox (Logica di business sui dati)
        $veicolo['url_immagine_formattata'] = str_replace(
            'dl=0', 
            'raw=1', 
            $veicolo['url_immagine'] ?? 'https://placehold.co/800x600?text=Immagine+Non+Disponibile'
        );

        // Controllo disponibilità
        $veicolo['is_disponibile'] = (strtolower($veicolo['stato']) == 'disponibile');

        return $veicolo;
    }
}