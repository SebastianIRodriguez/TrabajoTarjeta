<?php

namespace TrabajoTarjeta;

class Viaje implements ViajeInterface {
    
    private $valor;
    private $linea;
    private $fecha;
    private $tipo;
    
    public function __construct(float $valor, string $linea, int $fecha, int $tipo) {
        
        $this->valor = $valor;
        $this->linea = $linea;
        $this->fecha = $fecha;
        $this->tipo = $tipo;
    }
    
    public function getValor(): float {
        return $this->valor;
    }
    
    public function getTipo(): int {
        return $this->tipo;
    }
 
    public function getLinea(): string {
        return $this->linea;
    }
    
    public function getTiempo(): int {
        return $this->fecha;
    }
}

