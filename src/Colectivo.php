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

        $resultadoPago = false;

        if (($tarjeta->tipotarjeta() != 'medio universitario') && 
            ($tarjeta->tipotarjeta() != 'media franquicia estudiantil')){
            $resultadoPago = $tarjeta->pagar($this);
        }
        else {
            $resultadoPago = $tarjeta->pagoMedioBoleto($this);
        }
       
        if($resultadoPago == true){
            if ($tarjeta->usoplus() == true) {
                $boleto = new Boleto(
                    '0.0',
                    $this,
                    $tarjeta,
                    'viaje plus',
                    " ");
            } elseif($tarjeta->devolverUltimoTransbordo()){
                $boleto = new Boleto(
                    $tarjeta->devolverUltimoPago(),
                    $this,
                    $tarjeta,
                    "TRANSBORDO",
                    " ");
            }
            elseif ($tarjeta->MostrarPlusDevueltos() == 0) {
                $boleto = new Boleto(
                    $tarjeta->devolverUltimoPago(), 
                    $this,
                    $tarjeta,
                    $tarjeta->tipotarjeta(), " ");
            }
            else {
                $boleto = new Boleto(
                    $tarjeta->devolverUltimoPago(), 
                    $this, 
                    $tarjeta,
                    $tarjeta->tipotarjeta(),
                    "Paga " . (string) $tarjeta->MostrarPlusDevueltos() . " Viaje Plus");
            }
            $tarjeta->guardarUltimoBoleto($boleto);
        }

        return $boleto;
    } 
}