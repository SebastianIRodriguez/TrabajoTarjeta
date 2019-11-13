<?php

namespace TrabajoTarjeta;

trait TransbordoTrait {
    public function esTransbordo(Colectivo $colectivo, TiempoInterface $tiempo, Viaje $viaje) {
        return (
            ($viaje == null ||
            $colectivo->linea() != $viaje->getLinea()) &&
            $viaje->getTipo() != TipoViaje::TRANSBORDO &&
            $viaje->getTipo() != TipoViaje::VIAJE_PLUS &&
            $tiempo->getTiempo() - $viaje->getTiempo() < $this->tiempoTransbordo($tiempo));
    }

    public function tiempoTransbordo($tiempo) {
        if ($tiempo->esDiaSemana() && $tiempo->esFeriado() == FALSE) {
            return 60 * 60;
        }
        return 120 * 60;
    }

}