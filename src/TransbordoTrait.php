<?php

namespace TrabajoTarjeta;

trait TransbordoTrait {

    public function esTransbordo(Colectivo $colectivo, Viaje $viaje) {
        return (
            ($viaje == null ||
            $colectivo->linea() != $viaje->getLinea()) &&
            $viaje->getTipo() != TipoViaje::TRANSBORDO &&
            $viaje->getTipo() != TipoViaje::VIAJE_PLUS &&
            $this->tiempo->getTiempo() - $viaje->getTiempo() < $this->tiempoTransbordo());
    }

    public function tiempoTransbordo() {
        if ($this->tiempo->esDiaSemana() && $this->tiempo->esFeriado() == FALSE) {
            return 60 * 60;
        }
        return 120 * 60;
    }

}