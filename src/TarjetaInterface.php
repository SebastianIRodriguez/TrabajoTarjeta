<?php
namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Recarga una tarjeta con un cierto valor de dinero.
     *
     * @param float $monto
     *
     * @return bool
     *   Devuelve TRUE si el monto a cargar es válido, o FALSE en caso de que no sea valido.
     */
    public function recargar($monto);


    /**
     * @return float el saldo de la tarjeta
     */
    public function getSaldo(); 


    /**
     *  @return int ID de la tarjeta
     * */
    public function getId();


    public function getUltimoViaje(): ViajeInterface;


    /**
     * Devuelve TRUE y realiza las acciones correspondientes en caso de que podamos pagar un viaje
     * FALSE en caso contario
     * @return bool
     */
    public function pagar(Colectivo $colectivo);

}
