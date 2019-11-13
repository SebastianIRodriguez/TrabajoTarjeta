<?php
namespace TrabajoTarjeta;

class MedioBoleto extends Tarjeta {

  	public $monto = Tarifas::medio_boleto;

    public function getTipoTarjeta(){
        return 'media franquicia estudiantil';
    }

    use MedioBoletoTrait;


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

    if(!$this->sePuedePagarUnMedioBoleto()){
        return false;
    }

    $this->calcularCantBoletosDisponibles();

    $this->monto = $this->getMonto();

    $sePudoPagar = parent::pagar($colectivo);

    if($sePudoPagar &&
        $this->ultimoViaje->getTipo() == TipoViaje::NORMAL){

        $this->incrementarCantBoletosUsados();
    }

    return $sePudoPagar;
  }
}
