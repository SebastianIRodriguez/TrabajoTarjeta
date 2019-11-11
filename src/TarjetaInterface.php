<?php

namespace TrabajoTarjeta;

interface TarjetaInterface {

    /**
     * Recarga una tarjeta con un cierto valor de dinero.
     *
     * @param float $monto
     *
     * @return bool
     *   Devuelve TRUE si el monto a cargar es válido, o FALSE en caso de que no
     *   sea valido.
     */
    public function recargar($monto);

    /**
     * Devuelve el saldo que le queda a la tarjeta.
     *
     * @return float
     *      el saldo de la saldo
     */
    public function getSaldo(); 

    /**
     * Devuelve el tiempo actual en base al tiempo inyectado a la tarjeta
     * @return int
     *      tiempo
     */
    public function getTiempo();

    /**
     * Devuelve el tiempo en que se realizó el ultimo viaje
     * En caso de que sea el primer viaje de la tarjeta esta funcion retorna NULL
     * @return int
     *       El tiempo en el que se realizó el ultimo viaje.
     */
    public function getTiempoUltimoViaje();

    /**
     * Devuelve TRUE si el ultimo viaje realizo fue plus. Devuelve FALSE en caso contrario
     *
     * @return bool
     *          $Ultimoplus
     */
    public function usoplus();

    /**
     * Retorna la cantidad de dinero que usamos el ultimo viaje, que se encuentra almacenada
     * en la variable pago.
     * @return float
     *          Pago del ultimo viaje
     */
    public function getValorUltimoPago();

    /**
     * Devuelve el tipo de tarjeta, que puede ser:
     * -franquicia normal
     * -franquicia completa
     * -media franquicia estudiantil
     * -medio universitario
     *  @return string
     *              El tipo de tarjeta
     */
    public function getTipoTarjeta();

    /**
     * Almacena la cantidad de viajes plus que DEBEMOS
     *
     *   @return int
     *           la cantidad de plus que debemos
     */
    public function CantidadPlus();

    /**
     * Retorna TRUE en caso de que tengamos el saldo suficiente para pagar un viaje.
     * Retorna FALSE en caso contrario.
     *
     *  @return bool
     *          Condicion para pagar un viaje
     */
    public function saldoSuficiente();


    /**
     * Retorna TRUE en caso de que el ultimo viaje haya sido transbordo
     * Retorna FALSE en caso contrario.
     * @return bool
     *
     */
    public function ultimoViajeFueTransbordo();
    

    /**
     * Devuelve el tiempo maximo que tenemos para realizar un transbordo en base a la fecha y el horario en el
     * que nos encontremos
     *
     * @return int
     *              tiempo que tenemos para hacer transbordo
     *
     */
    public function tiempoTransbordo();

    /**
     * Devuelve TRUE si el viaje que vamos a pagar debe ser transbordo.
     * Devuelve FALSO en caso contrario
     *
     * @return bool
     */
    public function esTransbordo();

    /**
     * Devuelve la ID de nuestra tarjeta
     *  @return int
     *             ID de la tarjeta
     * */
    public function getId();

    /**
     * Devuelve el ultimo colectivo que hayamos viajado
     * @return ColectivoInterface
     *                  Ultimo colectivo en el que viajamos
     */
    public function getUltimoColectivo();

    /**
     * Devuelve TRUE en el caso de que el ultimo colectivo que viajamos sea igual al colectivo que
     * nos vamos a subir
     * FALSE en caso contrario
     *
     * @return bool
     */
    public function ColectivosIguales();

    public function getUltimoViaje(): ViajeInterface;

    /**
     * Devuelve TRUE y realiza las acciones correspondientes en caso de que podamos pagar un viaje
     * FALSE en caso contario
     * @return bool
     */
    public function pagar(Colectivo $colectivo);

}
