<?php

namespace TrabajoTarjeta;

class MedioBoletoUniversitario extends Tarjeta {

  public $monto = Tarifas::medio_boleto;

  public function getTipoTarjeta()
  {
      return 'medio universitario';
  }

  use MedioBoletoTrait;

  public function pagar(Colectivo $colectivo){

    if(!$this->sePuedePagarUnMedioBoleto()){
        return false;
    }

    $this->calcularCantBoletosDisponibles();

    $this->monto = $this->getMonto();

    $sePudoPagar = parent::pagar($colectivo);

    $tipoUltimoViaje = TipoViaje::NORMAL;

    if($this->ultimoViaje == null){
    	$tipoUltimoViaje = $this->ultimoViaje->getTipo();
    }

    if($sePudoPagar &&
        $tipoUltimoViaje == TipoViaje::NORMAL){

        $this->decrementarCantBoletosDisponibles();
    }

    return $sePudoPagar;
  }
}
