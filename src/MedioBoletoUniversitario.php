<?php

namespace TrabajoTarjeta;

class MedioBoletoUniversitario extends Tarjeta implements TarjetaInterface {
    
    protected $CantidadBoletos = 0;
    public $universitario = true;
    public $monto = 7.4;
    
    /**
     * Analiza si podemos realizar un pago, y que tipo de viaje vamos a haremos. 
     * Devuelve TRUE en caso de que podamos pagar un viaje y falso en caso contrario
     * 
     * @param Colectivo
     *              El colectivo en el que queremos pagar
     * @return bool
     *              Si se pudo pagar o no
     */
    public function pagoMedioBoleto(Colectivo $colectivo)
    {
        
          if ($this->DevolverUltimoTiempo() == NULL) {
            $this->iguales = FALSE;
        } else {
            if ($colectivo->linea() == $this->devolverUltimoColectivo()->linea()) {
                $this->iguales = TRUE;
            } else {
                $this->iguales = FALSE;
            }
        }
        if ($this->Horas() == FALSE) {
            
            if ($this->saldoSuficiente()) {
                
                if ($this->CantidadPlus() == 0) {
                    $this->CambioMonto();
                    $this->ultimoplus = FALSE;
                    $this->restarSaldo();
                    if ($this->devolverUltimoTransbordo() == FALSE && $this->tipotarjeta() == 'medio universitario') $this->IncrementarBoleto();
                    $this->ultimopago();
                    $this->reiniciarPlusDevueltos();
                    $this->ultimoTiempo    = $this->tiempo->reciente();
                    $this->ultimoColectivo = $colectivo;
                    return TRUE;
                }
                
                else {
                    
                    $this->ultimoplus   = FALSE;
                    $this->plusdevuelto = $this->CantidadPlus();
                    $this->restarSaldo();
                    $this->ultimopago();
                    $this->RestarPlus();
                    $this->ultimoTiempo    = $this->tiempo->reciente();
                    $this->ultimoColectivo = $colectivo; 
                    if($this->devolverUltimoTransbordo()==FALSE && $this->tipotarjeta()=='medio universitario') {
                      $this->IncrementarBoleto();
                    }
                    return TRUE;
                }
                
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
        
        if ($this->tiempo->reciente() - $this->DevolverUltimoTiempo() > 5 * 60) {
            if ($this->saldoSuficiente()) {
                
                if ($this->CantidadPlus() == 0) {
                    $this->CambioMonto();
                    $this->restarSaldo(); //restamos el saldo 
                    $this->ultimopago(); //guardamos el ultimo pago
                    $this->reiniciarPlusDevueltos(); //reiniciamos la cantidad de viajes plus
                    if($this->devolverUltimoTransbordo()==FALSE && $this->tipotarjeta()=='medio universitario') {
                      $this->IncrementarBoleto();
                    }
                    ; //si el viaje no es transbordo,aumentamos en 1 la cantidad de boletos que podemos usar en el dia
                    $this->ultimoTiempo    = $this->tiempo->reciente(); //almacenamos el ultimo tiempo
                    $this->ultimoplus      = FALSE;
                    $this->ultimoColectivo = $colectivo;
                    return TRUE;
                }
                else {
            
                    $this->plusdevuelto = $this->CantidadPlus();
                    $this->restarSaldo();
                    $this->ultimopago();
                    $this->RestarPlus();
                    $this->ultimoTiempo    = $this->tiempo->reciente();
                    $this->ultimoplus      = FALSE;
                    $this->ultimoColectivo = $colectivo; 
                    if($this->devolverUltimoTransbordo()==FALSE && $this->tipotarjeta()=='medio universitario') {
                      $this->IncrementarBoleto();
                    }
                    return TRUE;
                }
                
            }
            else {
                
                if ($this->CantidadPlus() < 2) {
                    $this->plusdevuelto = 0;
                    $this->ultimoplus   = TRUE;
                    $this->IncrementoPlus();
                    $this->ultimoTiempo    = $this->tiempo->reciente();
                    $this->ultimoColectivo = $colectivo;
                    return TRUE;
                    
                }
                
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
        if ($this->ViajesRestantes() == TRUE) {
            $this->monto = 7.4;
            return $this->monto;
        }
        $this->monto = 14.8;
        return $this->monto;
    }
    
    /**
     * Incrementa en 1 la cantidad de medios boletos que usamos en el dia
     *  @return int
     *              cantidad de medios boletos usados en el dia
     */
    public function IncrementarBoleto() {
        
        $this->CantidadBoletos += 1;
        return $this->CantidadBoletos;
    }
    
    /**
     * Reinicia la cantidad de boletos que podemos usar a 0
     */
    public function ReiniciarBoleto() {
        
        $this->CantidadBoletos = 0;
        
        
    }
    
    /**
     * Devuelve TRUE si nos quedan medios boletos para usar y FALSE en caso contrario
     * @return bool         
     *            
     */
    public function ViajesRestantes() {
        if ($this->CantidadBoletos < 2) {
                    return TRUE;
        }
        else {
                    return FALSE;
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
    public function Horas()
    {
        
        if($this->tipotarjeta()!= 'medio universitario') return FALSE;
        else{ if ($this->DevolverUltimoTiempo() != NULL) {
            
            if ($this->tiempo->reciente() - $this->DevolverUltimoTiempo() < 60 * 60 * 24) {
                return TRUE;
                
            }
            
            $this->ReiniciarBoleto();
            return FALSE; 
        }
      } 
        
    }
    
    
}