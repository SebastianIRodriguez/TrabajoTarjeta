<?php

namespace TrabajoTarjeta;

class FranquiciaCompleta extends Tarjeta {

public $monto = 0.0;

    public function tipotarjeta()
    {
        return 'franquicia completa';
    }

}
