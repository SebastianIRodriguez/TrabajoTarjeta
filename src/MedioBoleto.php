<?php
namespace TrabajoTarjeta;

class MedioBoleto extends Tarjeta {

  public $monto = Tarifas::medio_boleto;

    public function getTipoTarjeta()
    {
        return 'media franquicia estudiantil';
    }

    use MedioBoletoTrait;
}
