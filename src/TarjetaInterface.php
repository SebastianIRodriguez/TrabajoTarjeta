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
    public function obtenerSaldo(); 

    /**
     * Devuelve el tiempo actual en base al tiempo inyectado a la tarjeta
     * @return int
     *      tiempo 
     */
    public function getTiempo(); 

    /**
     *
     * @return int 
     *      la cantidad de viajes plus que se DEVOLVIERON el ultimo viaje.
     *      
     */
    public function MostrarPlusDevueltos();  


    /**
     * Reinicia la variable que almacena la cantidad de plus que se devolvieron a 0.
     * Esta funcion no retorna nada
     */
    public function reiniciarPlusDevueltos();

    /**
     * Devuelve el tiempo en que se realizó el ultimo viaje
     * En caso de que sea el primer viaje de la tarjeta esta funcion retorna NULL
     * 
     * @return int 
     *       El tiempo en el que se realizó el ultimo viaje.
     */
    public function DevolverUltimoTiempo(); 

    /**
     * Devuelve TRUE si el ultimo viaje realizo fue plus. Devuelve FALSE en caso contrario
     * 
     * @return bool
     *          $Ultimoplus
     */
    public function usoplus();

    /**
     * Guarda en la variable pago la cantidad de dinero que gastamos en el ultimo viaje.
     * Esta funcion solo procesa. No retorna nada
     */
    public function ultimopago();

    /**
     * Retorna la cantidad de dinero que usamos el ultimo viaje, que se encuentra almacenada 
     * en la variable pago.
     * @return float
     *          Pago del ultimo viaje
     */
    public function devolverUltimoPago(); 

    /**
     * Devuelve el tipo de tarjeta, que puede ser:
     * -franquicia normal
     * -franquicia completa
     * -media franquicia estudiantil
     * -medio universitario
     *  @return string
     *              El tipo de tarjeta
     */ 
    public function tipotarjeta();

    /**
     * Almacena la cantidad de viajes plus que DEBEMOS
     * 
     *   @return int
     *           la cantidad de plus que debemos
     */
    public function CantidadPlus();

    /**Incrementa en 1 la cantidad de plus que debemos. Esta funcion no retorna nada */
    public function IncrementoPlus();

    /**
     * Hace que la cantidad de plus que debemos pase a ser 0.
     * Esta funcion solo procesa.
     */
    public function RestarPlus();

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
    public function devolverUltimoTransbordo();

    /**
     * Devuelve el monto que vale el transbordo.
     * @return float
     *              el monto del transbordo
     */
    public function devolverMontoTransbordo();

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
     * Resta el saldo a nuestra tarjeta despues de pagar un viaje
     */
    public function restarSaldo();

    /**
     * Devuelve la ID de nuestra tarjeta
     *  @return int
     *             ID de la tarjeta
     * */
    public function obtenerID();

    /**
     * Guarda el ultimo boleto que nos devolvieron al pagar
     */
    public function guardarUltimoBoleto($boleto);

    /**
     * Devuelve el ultimo colectivo que hayamos viajado
     * @return ColectivoInterface
     *                  Ultimo colectivo en el que viajamos
     */
    public function devolverUltimoColectivo();

    /**
     * Devuelve TRUE en el caso de que el ultimo colectivo que viajamos sea igual al colectivo que 
     * nos vamos a subir
     * FALSE en caso contrario
     * 
     * @return bool
     */
    public function ColectivosIguales();

    /**
     * Devuelve TRUE y realiza las acciones correspondientes en caso de que podamos pagar un viaje
     * FALSE en caso contario
     * @return bool
     */
    public function pagar(Colectivo $colectivo);

}
