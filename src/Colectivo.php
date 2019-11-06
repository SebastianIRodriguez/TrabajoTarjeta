<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {
    
    private $linea;
    private $empresa;
    private $numero;
    
    /**
     * Constructor del boleto
     */
    public function __construct($l, $e, $n) {
        $this->linea   = $l;
        $this->empresa = $e;
        $this->numero  = $n;
    }
    
    public function linea() {
        return $this->linea;
    }
    
    public function empresa() {
        return $this->empresa;
    }
    
    public function numero() {
        return $this->numero;
    }

    
    public function pagarCon(TarjetaInterface $tarjeta) {

        $boleto = false;
        $resultadoPago = $tarjeta->pagar($this);
       
        if($resultadoPago == true){

            $valor = "";
            $tipotarjeta = "";
            $descripcion = " ";

            if ($tarjeta->usoplus() == true) {
                $valor = "0.0";
                $tipotarjeta = "VIAJE PLUS";
            }
            elseif($tarjeta->devolverUltimoTransbordo()){
                $valor = $tarjeta->devolverUltimoPago();
                $tipotarjeta = "TRANSBORDO";
            }
            elseif ($tarjeta->MostrarPlusDevueltos() == 0) {
                $valor = $tarjeta->devolverUltimoPago();
                $tipotarjeta = $tarjeta->tipotarjeta();
            }
            else {
                $valor = $tarjeta->devolverUltimoPago();
                $tipotarjeta = $tarjeta->tipotarjeta();
                $descripcion = "Paga " . (string) $tarjeta->MostrarPlusDevueltos() . " Viaje Plus";
            }

            $boleto = new Boleto(
                    $valor,
                    $this->linea,
                    $tarjeta->obtenerID(),
                    $tarjeta->obtenerSaldo(),
                    $tarjeta->DevolverUltimoTiempo(),
                    $tipotarjeta,
                    $descripcion);

            $tarjeta->guardarUltimoBoleto($boleto);
        }

        return $boleto;
    } 
}