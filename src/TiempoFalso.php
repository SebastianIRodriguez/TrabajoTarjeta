<?php

namespace TrabajoTarjeta;

class TiempoFalso implements TiempoInterface {


    protected $tiempo;
    protected $estado = false;
    protected $estadoDiaSemana;


    public function __construct(int $tiempoInicial = 0) {
        $this->tiempo = $tiempoInicial;
    }

    /**
     * Devuelve el estado en el que se encuentran las funciones feriado, noche y fin de semana
     *  @return bool
     *              estado
     */
    public function devolverEstado() {
        return $this->estado;
    }

    public function getTiempo() {
        return $this->tiempo;
    }

    /**
     * Cambia los estados de las funciones feriado noche y fin de semana a TRUE
     * De esta forma activamos los transbordos de 90 minutos
     * @return TRUE
     */
    public function setTrue() {
        $this->estado = TRUE;
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
        return $this->estado;
    }

    /**
     * Avanza el tiempo
     * @param int Cant de segundos a avanzar
     */
    public function avanzarSegundos($segundos) {
        $this->tiempo += $segundos;
    }


    /**
     * Avanza el tiempo
     * @param int Cant de minutos a avanzar
     */
    public function avanzarMinutos($minutos) {
        $this->tiempo += $minutos * 60;
    }


    /**
     * Avanza el tiempo
     * @param int Cant de horas a avanzar
     */
    public function avanzarHoras($horas) {
        $this->tiempo += $horas * 60 * 24;
    }
}
