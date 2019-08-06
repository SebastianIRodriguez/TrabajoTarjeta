<?php

namespace TrabajoTarjeta;

class TiempoFalso implements TiempoInterface {
    
    
    protected $tiempo;
    protected $estado = false;
    protected $estadoDiaSemana;
    
    
    public function __construct($IniciarEn = 0) {
        
        $this->tiempo = $IniciarEn;
        
    }
    
    /**
     * Devuelve el estado en el que se encuentran las funciones feriado, noche y fin de semana
     *  @return bool 
     *              estado
     */
    public function devolverEstado() {
        return $this->estado;
    }
    
    public function reciente() {
        
        return $this->tiempo;
    }
    
    /**
     * Cambia los estados de las funciones feriado noche y fin de semana a TRUE
     * De esta forma activamos los transbordos de 90 minutos
     * @return TRUE
     */
    public function setTrue(TiempoFalso $EstadoASetear) {
        $EstadoASetear->estado = TRUE;
        return $EstadoASetear;
    }
    
    /**
     * Devuelve TRUE en caso de que sea feriado. FALSE en caso contrario
     * 
     * @return bool 
     *              
     */
    public function esFeriado() {
        return $this->estado;
    }


    /**
     * Devuelve TRUE en caso de que sea de noche. FALSE en caso contrario
     * @return bool
     */
    public function esDeNoche() {
        return $this->estado;
    }
    
    /**
     * Devuelve TRUE en caso de que sea fin de semana. FALSE en caso contrario
     * @return bool
     */
    public function esFinDeSemana() {
        return $this->estado;
    }
    
    /**
     * Devuelve TRUE en caso de que sea dia de semana. FALSE en caso contrario
     * @return bool
     */
    public function esDiaSemana() {
        if ($this->estado) {
                    $this->estadoDiaSemana = FALSE;
        }
        else {
                    $this->estadoDiaSemana = TRUE;
        }
        return $this->estadoDiaSemana;
    }
    
    /**
     * Avanza nuestra funcion X segundos
     *
     * @param int 
     *              segundos a avanzar el tiempo
     */
    public function Avanzar($segundos) {
        
        $this->tiempo += $segundos;
    }
    
    
}