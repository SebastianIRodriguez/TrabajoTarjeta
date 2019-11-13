<?php

namespace TrabajoTarjeta;

trait MedioBoletoTrait {

  public $cantidadBoletosFranquicia = 0;

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


  public function sePuedePagarUnMedioBoleto(): Boolean {

    $tiempoUltimoViaje = $this->getTiempoUltimoViaje();

    if($this->tiempo->getTiempo() - $tiempoUltimoViaje < 5 * 60 ){
      return false;
    }
    return true;
  }


  private function getTiempoUltimoViaje() {
    if ($this->ultimoViaje == NULL) {
      return $tiempoUltimoViaje = -INF;
    }

    return $this->ultimoViaje->getTiempo();
  }

  public function calcularCantBoletosDisponibles(){
    if($this->tiempo->getTiempo() - $this->getTiempoUltimoViaje() > 24 * 60 * 60){
        $this->cantidadBoletosFranquicia = 0;
    }
  }

  public function decrementarCantBoletosDisponibles(){
    $this->cantidadBoletosFranquicia--;
  }
}
