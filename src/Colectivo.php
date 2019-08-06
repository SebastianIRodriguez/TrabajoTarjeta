<?php

namespace TrabajoTarjeta;

class Colectivo implements ColectivoInterface {
    
    protected $linea;
    protected $empresa;
    protected $numero;
    
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
        
        if (($tarjeta->tipotarjeta() != 'medio universitario') && ($tarjeta->tipotarjeta() != 'media franquicia estudiantil')) {
            if ($tarjeta->pagar($this) == true) {
                
                if ($tarjeta->usoplus() == true) {
                    $boleto = new Boleto('0.0', $this, $tarjeta, 'viaje plus', " ");
                    $tarjeta->guardarUltimoBoleto($boleto);
                    return $boleto;
                } else {
                    if($tarjeta->devolverUltimoTransbordo()){
                        $boleto = new Boleto($tarjeta->devolverUltimoPago(), $this, $tarjeta, "TRANSBORDO", " ");
                        $tarjeta->guardarUltimoBoleto($boleto);
                        return $boleto;
                      }
                        else{ 
                    if ($tarjeta->MostrarPlusDevueltos() == 0) {
                        $boleto = new Boleto($tarjeta->devolverUltimoPago(), $this, $tarjeta, $tarjeta->tipotarjeta(), " ");
                        $tarjeta->guardarUltimoBoleto($boleto);
                        return $boleto;
                    }
                    
                    else {
                        $boleto = new Boleto($tarjeta->devolverUltimoPago(), $this, $tarjeta, $tarjeta->tipotarjeta(), "Paga " . (string) $tarjeta->MostrarPlusDevueltos() . " Viaje Plus");
                        $tarjeta->guardarUltimoBoleto($boleto);
                        return $boleto;
                        
                    }
                        }
                    
                }
                
                
            }
            return FALSE;
            
        }
        else {
            if ($tarjeta->pagoMedioBoleto($this) == TRUE) {
                
                if ($tarjeta->usoplus() == TRUE) {
                    $boleto = new Boleto('0.0', $this, $tarjeta, 'viaje plus', " ");
                    $tarjeta->guardarUltimoBoleto($boleto);
                    return $boleto;
                } else {
                    if($tarjeta->devolverUltimoTransbordo()){
                        $boleto = new Boleto($tarjeta->devolverUltimoPago(), $this, $tarjeta, "TRANSBORDO", " ");
                        $tarjeta->guardarUltimoBoleto($boleto);
                        return $boleto;
                      }
                        else{ 
                    if ($tarjeta->MostrarPlusDevueltos() == 0) {
                        $boleto = new Boleto($tarjeta->devolverUltimoPago(), $this, $tarjeta, $tarjeta->tipotarjeta(), " ");
                        $tarjeta->guardarUltimoBoleto($boleto);
                        return $boleto;
                    }
                    
                    else {
                        $boleto = new Boleto($tarjeta->devolverUltimoPago(), $this, $tarjeta, $tarjeta->tipotarjeta(), "Paga " . (string) $tarjeta->MostrarPlusDevueltos() . " Viaje Plus");
                        $tarjeta->guardarUltimoBoleto($boleto);
                        return $boleto;
                        
                    }
                        }
                    
                }
            }
            
        }
        
        
        return false;
        
        
    }
    
}
