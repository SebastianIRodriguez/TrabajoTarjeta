<?php

namespace TrabajoTarjeta;

class Boleto implements BoletoInterface {
    
    protected $valor;
    protected $linea;
    protected $fecha;
    protected $hora;
    protected $saldo;
    protected $id;
    protected $tipo;
    protected $descripcion;
    protected $timeult;
    
    public function __construct($valor, $linea, $id, $saldo, $fecha, $tipo, $descripcion) {
        
        $this->valor = $valor;
        $this->linea = $linea;
        $this->id = $id;
        $this->saldo = $saldo;
        $this->fecha = date('d-m-Y', $fecha);
        $this->descripcion = $descripcion;
        $this->tipo = $tipo;
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
        return $this->linea;
        
    }
    
    public function obtenerFecha() {
        return $this->fecha;
    }
    
}

