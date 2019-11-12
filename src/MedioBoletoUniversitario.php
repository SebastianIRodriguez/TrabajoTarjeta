<?php

namespace TrabajoTarjeta;

class MedioBoletoUniversitario extends Tarjeta {

  protected $CantidadBoletos = 0;
  public $monto = Tarifas::medio_boleto;

  public function getTipoTarjeta()
  {
      return 'medio universitario';
  }

  use MedioBoleto;
}
