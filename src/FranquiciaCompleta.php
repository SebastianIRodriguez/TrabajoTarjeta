<?php

namespace TrabajoTarjeta;

class FranquiciaCompleta extends Tarjeta {

public $monto = 0.0;
private $saldo = Tarifas::boleto;

    public function getTipoTarjeta()
    {
        return 'franquicia completa';
    }

}
