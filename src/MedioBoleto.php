<?php
namespace TrabajoTarjeta;

class MedioBoleto extends MedioBoletoUniversitario {

  public $monto = Tarifas::medio_boleto;

    public function getTipoTarjeta()
    {
        return 'media franquicia estudiantil';
    }

    use MedioBoleto;

}
