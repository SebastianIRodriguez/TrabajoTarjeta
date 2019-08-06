<?php

namespace TrabajoTarjeta;

interface BoletoInterface {

    /**
     * Devuelve el valor del boleto.
     *
     * @return int
     */
    public function obtenerValor();

    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     *
     * @return ColectivoInterface
     */
    public function obtenerColectivo(); 


    /**
     * Devuelve el tipo del viaje. Que puede ser viaje plus, transbordo, franquicia normal, franquicia media 
     * y franquicia completa
     * @return string
     */
    public function obtenerTipo(); 

    /**
     * Devuelve la fecha en la que sea realizó el ultimo viaje
     *   @return int 
     */
    public function obtenerFecha();

}
