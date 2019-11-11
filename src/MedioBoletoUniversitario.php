<?php

namespace TrabajoTarjeta;

class MedioBoletoUniversitario extends Tarjeta {

  protected $cantidadBoletosFranquicia = 0;
  public $monto = Tarifas::medio_boleto;

  public function getTipoTarjeta()
  {
      return 'medio universitario';
  }

  /**
   * Analiza si podemos realizar un pago, y que tipo de viaje vamos a haremos.
   * Devuelve TRUE en caso de que podamos pagar un viaje y falso en caso contrario
   *
   * @param Colectivo
   *              El colectivo en el que queremos pagar
   * @return bool
   *              Si se pudo pagar o no
   */
  public function pagar(Colectivo $colectivo){

    if($this->tiempo->getTiempo() - $this->ultimoViaje->getTiempo() < 5 * 60 ){
        return false;
    }

    if($this->tiempo->getTiempo() - $this->ultimoViaje->getTiempo() > 24 * 60 * 60){
        $this->cantidadBoletosFranquicia = 0;
    }

    $sePudoPagar = parent::pagar();

    if($sePudoPagar && 
        $this->ultimoViaje->getTipo() == TipoViaje::NORMAL){

        $this->cantidadBoletosFranquicia++;
    }

    return $sePudoPagar;
  }


  /**
   * Cambia el monto de nuestra tarjeta dependiendo de la cantidad de viajes
   * que hayamos usado y la hora con respecto al ultimo viaje.
   *
   * @return float
   *              monto a pagar en el viaje
   */
  public function getMonto() {
      if ($this->cantidadBoletosFranquicia < 2) {
          return Tarifas::medio_boleto;
      }
      return Tarifas::boleto;
  }
}
