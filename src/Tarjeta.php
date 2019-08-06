<?php

namespace TrabajoTarjeta;

class Tarjeta implements TarjetaInterface {
    
    protected $saldo = 0;
    public $monto = 14.8;
    protected $viajeplus = 0;
    protected $ID;
    protected $ultboleto = null;
    protected $tipo = 'franquicia normal';
    protected $tiempo;
    protected $ultimoplus = false;
    protected $fechault;
    protected $pago = 0;
    protected $plusdevuelto = 0;
    public $universitario = false;
    protected $ultimoTiempo = null;
    protected $montoTransbordo;
    protected $tiempoTr;
    protected $ultimoTransbordo = false;
    protected $colec;
    protected $ultimoColectivo = null;
    protected $iguales = false;
    
    
    public function __construct(TiempoInterface $tiempo) {
        $this->saldo     = 0.0;
        $this->viajeplus = 0;
        $this->ID        = rand(0, 100);
        $this->ultboleto = null;
        $this->tiempo    = $tiempo;
    }
    
    
    public function getTiempo() {
        return $this->tiempo->reciente();
    }
    
    public function MostrarPlusDevueltos() {
        
        return $this->plusdevuelto; 
    }
    
    public function DevolverUltimoTiempo() {
        
        return $this->ultimoTiempo;
    }
    
    public function reiniciarPlusDevueltos() {
        
        $this->plusdevuelto = 0;
    }
    
    public function usoplus() {
        
        return $this->ultimoplus; 
    }
    
    public function ultimopago() {
        if ($this->devolverUltimoTransbordo()) {
          $this->pago = ($this->monto * 0.33);
        }
            
       
        else {
          $this->pago = $this->monto + 14.8*$this->MostrarPlusDevueltos();
        }
            
        
    }
    
    public function devolverUltimoPago() {
        
        return $this->pago;
    } 
    
    public function tipotarjeta() 
    {
        
        if ($this->monto == 14.8) {
            return $this->tipo;
        }
        else {
            if ($this->monto == 7.4) {
                
                if ($this->universitario == TRUE) {
                    $this->tipo = 'medio universitario';
                    return $this->tipo;
                }
                $this->tipo = 'media franquicia estudiantil';
                return $this->tipo;
            }
            $this->tipo = 'franquicia completa';
            return $this->tipo;
        }
        
    }
    
    public function CantidadPlus() {
        return $this->viajeplus; //devuelve la cantidad de viajes plus que adeudamos
        
    }
    
    
    public function IncrementoPlus() {
        
        $this->viajeplus += 1;
    }
    
    public function RestarPlus() {
        
        $this->viajeplus = 0;
    }
    
    
    public function saldoSuficiente() {
        if ($this->obtenerSaldo() >= ($this->monto + $this->CantidadPlus() * 14.8)) {
            return TRUE;
        }
        return FALSE;
        
    } //indica si tenemos saldo suficiente para pagar un viaje
    
    public function obtenerSaldo() {
        return $this->saldo;
    }
    
    public function devolverUltimoTransbordo() {
        
        return $this->ultimoTransbordo;
    }
    public function devolverMontoTransbordo() {
        $this->montoTransbordo = ($this->monto*0.33);
        return $this->montoTransbordo;
    }
    
    public function tiempoTransbordo() {
        if ($this->tiempo->esDiaSemana() && $this->tiempo->esFeriado() == FALSE) {
            $tiempoTr = 60 * 60;
            return $tiempoTr;
        }
        
        $tiempoTr = 90 * 60;
        return $tiempoTr;
    }
    
    public function esTransbordo() {
        
        if ($this->usoplus() == FALSE && $this->ColectivosIguales() == FALSE && $this->devolverUltimoTransbordo() == FALSE) {
            
            
            if ($this->tiempo->reciente() - $this->DevolverUltimoTiempo() < $this->tiempoTransbordo()) {
                
                return TRUE;
            }
        }
        
        return FALSE;
    } 
    
    public function restarSaldo() {
        if ($this->DevolverUltimoTiempo() == NULL) {
            
            
            $this->saldo -= $this->monto;
            $this->viajeplus        = 0;
            $this->ultimoTransbordo = FALSE;
        }
        else {
            
            if ($this->esTransbordo()) {
                
                
                $this->montoTransbordo = ($this->monto * 0.33);
                $this->saldo -= $this->montoTransbordo;
                $this->ultimoTransbordo = TRUE;
            }
            else {
                
                $this->saldo -= ($this->monto + $this->CantidadPlus() * 14.8);
                $this->viajeplus        = 0;
                $this->ultimoTransbordo = FALSE;
            }
            
        }
    }
    
    public function obtenerID() {
        return $this->ID;
    }
    
    public function guardarUltimoBoleto($boleto) {
        $this->ultboleto = $boleto;
    }
    
    public function devolverUltimoColectivo() {
        return $this->ultimoColectivo;
    }
    
    public function ColectivosIguales() {
        return $this->iguales;
    }
    
    
    public function pagar(Colectivo $colectivo) {
        
        if ($this->DevolverUltimoTiempo() == NULL) {
            $this->iguales = FALSE;
        }
        else {
            if ($colectivo->linea() == $this->devolverUltimoColectivo()->linea()) {
                $this->iguales = TRUE;
            }
            else {
                $this->iguales = FALSE;
            }
        }
        
        if ($this->saldoSuficiente()) {
            
            if ($this->usoplus() == FALSE) {
                $this->restarSaldo();
                $this->ultimopago();
                $this->plusdevuelto    = 0;
                $this->ultimoplus      = FALSE;
                $this->ultimoTiempo    = $this->tiempo->reciente();
                $this->ultimoColectivo = $colectivo;
            }
            else {
                $this->plusdevuelto = $this->CantidadPlus();
                $this->restarSaldo();
                $this->ultimopago();
                $this->RestarPlus();
                $this->ultimoplus      = false;
                $this->ultimoTiempo    = $this->tiempo->reciente();
                $this->ultimoColectivo = $colectivo;
            }
            
            return true;
            
        }
        else {
            
            if ($this->CantidadPlus() < 2) {
                $this->plusdevuelto = 0;
                $this->ultimoplus   = true;
                $this->IncrementoPlus();
                $this->ultimoTiempo    = $this->tiempo->reciente();
                $this->ultimoColectivo = $colectivo;
                return true;
            }
            return false;
            
        }
        
    }
    
    public function recargar($monto) {
        
        if ($monto == 10 || $monto == 20 || $monto == 30 || $monto == 50 || $monto == 100 || $monto == 510.15 || $monto == 962.59) {
            if ($monto == 962.59) {
                $this->saldo += ($monto + 221.58);
                return true;
            }
            else {
                if ($monto == 510.15) {
                    $this->saldo += ($monto + 81.93);
                    return true;
                }
                else {
                    $this->saldo += $monto;
                    return true;
                }
            }
            
        }
        else {
            return false;
            
        }
        
    }
    
}