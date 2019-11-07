<?php

namespace TrabajoTarjeta;

class MedioBoletoUniversitario extends Tarjeta {

  protected $CantidadBoletos = 0;
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

    $this->iguales = (
        ($this->getTiempoUltimoViaje() != null) &&
        ($colectivo->linea() == $this->getUltimoColectivo()->linea()));
    
    if (!$this->Horas() ||
      $this->tiempo->getTiempo() - $this->getTiempoUltimoViaje() > 5 * 60) {

      if ($this->saldoSuficiente()) {

        $this->ultimoplus = false;

        if ($this->CantidadPlus() == 0) {

            $this->CambioMonto();
            $this->restarSaldo();
            $this->ultimopago();
            $this->plusdevuelto = 0;
        }
        else {
            $this->plusdevuelto = $this->CantidadPlus();
            $this->restarSaldo();
            $this->ultimopago();
            $this->viajeplus = 0;
        }

        $this->IncrementarBoleto();
        $this->ultimoTiempo = $this->tiempo->getTiempo();
        $this->ultimoColectivo = $colectivo;
        return true;
      }
      elseif ($this->CantidadPlus() < 2) {

        $this->plusdevuelto = 0;
        $this->ultimoplus = true;
        $this->viajeplus += 1;
        $this->ultimoTiempo = $this->tiempo->getTiempo();
        $this->ultimoColectivo = $colectivo;

        return true;
      }
    }
    return false;
  }


  /**
   * Cambia el monto de nuestra tarjeta dependiendo de la cantidad de viajes
   * que hayamos usado y la hora con respecto al ultimo viaje.
   *
   * @return float
   *              monto a pagar en el viaje
   */
  public function CambioMonto() {

      $this->Horas();
      if ($this->CantidadBoletos < 2) {
          $this->monto = Tarifas::medio_boleto;
          return $this->monto;
      }
      $this->monto = Tarifas::boleto;
      return $this->monto;
  }


  /**
   * Incrementa en 1 la cantidad de medios boletos que usamos en el dia
   */
  private function IncrementarBoleto() {
    if($this->ultimoViajeFueTransbordo()==FALSE && 
      $this->getTipoTarjeta()=='medio universitario') {

      $this->CantidadBoletos += 1;
    }
  }

  /**
   * @return int
   *              la cantidad de medios boletos que usamos en el dia
   */
  public function DevolverCantidadBoletos() {
    return $this->CantidadBoletos;
  }


  /**
   * Horas devuelve falso cuando la tarjeta realizará su primer pago, o cuando haya pasado mas de 24 horas
   * con respecto al ultimo pago. Si pasaron mas de 24 horas reinicia la cantidad de boletos.
   *
   * Horas tambien devuelve FALSE en caso de que la tarjeta usada no sea de tipo medio universitario
   *
   * @return bool
   *
   */
  public function Horas(){

    if($this->getTipoTarjeta()!= 'medio universitario') return FALSE;

    if ($this->getTiempoUltimoViaje() != NULL) {

      if ($this->tiempo->getTiempo() - $this->getTiempoUltimoViaje() < 60 * 60 * 24) {
          return TRUE;
      }

      $this->CantidadBoletos = 0;
      return FALSE;
    }
  }
}
