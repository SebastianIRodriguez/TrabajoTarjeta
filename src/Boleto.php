<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {
    
    protected $valor;
    protected $colectivo;
    protected $fecha;
    protected $hora;
    protected $saldo;
    protected $id;
    protected $tipo;
    protected $descripcion;
    protected $timeult;
    
    public function __construct($valor, $colectivo, $tarjeta, $tipo, $descripcion) {
        if($tarjeta->devolverUltimoTransbordo()) {
          $this->valor = $tarjeta->devolverMontoTransbordo();
        }
        else {
          $this->valor = $tarjeta->devolverUltimoPago();
        }
        $this->colectivo   = $colectivo->linea();
        $this->saldo       = $tarjeta->obtenerSaldo();
        $this->id          = $tarjeta->obtenerID();
        $this->fecha       = date('d-m-Y', $tarjeta->DevolverUltimoTiempo());
        $this->descripcion = $descripcion;
        if ($tarjeta->usoplus() == TRUE) {
            $this->tipo = "VIAJE PLUS";
        }
        else {
            if ($tarjeta->devolverUltimoTransbordo()) {
                $this->tipo = "TRANSBORDO";
            }
            else {
                $this->tipo = $tarjeta->tipotarjeta();
            }
        }
        
        
    }
    
    /**
     * Devuelve el valor del boleto.
     *
     * @return int
     */
    
    public function obtenerValor() {
        return $this->valor;
    }
    
    
    public function obtenerTipo() {
        return $this->tipo;
    }
    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     *
     * @return ColectivoInterface
     */
    
    public function obtenerColectivo() {
        return $this->colectivo;
        
    }
    
    public function obtenerFecha() {
        return $this->fecha;
    }
    
}

