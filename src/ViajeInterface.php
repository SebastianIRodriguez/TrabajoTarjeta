<?php

namespace TrabajoTarjeta;

interface ViajeInterface {

    /**
     * Devuelve el valor del boleto.
     *
     * @return int
     */
    public function getValor() : float;


    /**
     * Devuelve un objeto que respresenta el colectivo donde se viajó.
     */
    public function getLinea() : string; 


    /**
     * Devuelve el tipo del viaje.
     */
    public function getTipo(): int; 


    public function getTiempo(): int;

}
