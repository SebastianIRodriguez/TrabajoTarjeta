<?php
namespace TrabajoTarjeta;
class Tarjeta implements TarjetaInterface {

    private $saldo = 0;
    public $monto = Tarifas::boleto;
    protected $viajeplus = 0;
    protected $ID;
    protected $ultboleto = null;
    protected $tipo = 'franquicia normal';
    protected $tiempo;
    protected $iguales = false;
    protected $ultimoViaje = null;


    public function __construct(TiempoInterface $tiempo) {
        $this->ID = rand(0, 100);
        $this->tiempo = $tiempo;
    }

    public function getUltimoViaje(): ViajeInterface{
        return $this->ultimoViaje;
    }

    public function getTiempo() {
        return $this->tiempo->getTiempo();
    }

    public function getTiempoUltimoViaje() {
        return $this->ultimoViaje->getTiempo();
    }

    public function getValorUltimoPago() {
        return $this->ultimoViaje->getValor();
    }

    public function getUltimoColectivo() {
        return $this->ultimoViaje->getLinea();
    }

    public function usoplus() {
        return $this->ultimoViaje->getTipo() == TipoViaje::VIAJE_PLUS;
    }

    public function getTipoTarjeta() {
        return $this->tipo;
    }

    public function getSaldo() {
        return $this->saldo;
    }

    public function getId() {
        return $this->ID;
    }


    //devuelve la cantidad de viajes plus que adeudamos
    public function CantidadPlus() {
        return $this->viajeplus;
    }

    //indica si tenemos saldo suficiente para pagar un viaje
    public function saldoSuficiente() {
        return ($this->saldo >= ($this->monto + $this->viajeplus * Tarifas::boleto));
    }


    public function ultimoViajeFueTransbordo() {
        return $this->ultimoViaje->getTipo() == TipoViaje::TRANSBORDO;
    }

    public function tiempoTransbordo() {
        if ($this->tiempo->esDiaSemana() && $this->tiempo->esFeriado() == FALSE) {
            return 60 * 60;
        }
        return 90 * 60;
    }

    public function esTransbordo() {
        return ($this->usoplus() == FALSE &&
            $this->ColectivosIguales() == FALSE &&
            $this->ultimoViajeFueTransbordo() == FALSE &&
            $this->tiempo->getTiempo() - $this->getTiempoUltimoViaje() < $this->tiempoTransbordo());
    }

    public function ColectivosIguales() {
        return $this->iguales;
    }

    public function pagar(Colectivo $colectivo) {

        $sePudoPagar = false;
        $montoAPagar = 0.0;
        $tipo = null;

        $this->iguales = (
            ($this->ultimoViaje != null) &&
            ($colectivo->linea() == $this->ultimoViaje->getLinea()));

        //Si tengo para pagar
        if ($this->saldoSuficiente()) {

            if ($this->ultimoViaje == NULL) {
                $montoAPagar = $this->monto;
                $tipo = TipoViaje::NORMAL;
            }
            elseif ($this->esTransbordo()) {
                $montoAPagar = Tarifas::transbordo;
                $tipo = TipoViaje::TRANSBORDO;
            }
            else {
                $montoAPagar = ($this->monto + $this->viajeplus * Tarifas::boleto);
                $this->viajeplus = 0;
                $tipo = TipoViaje::NORMAL;
            }
            $this->saldo -= $montoAPagar;
 
            $sePudoPagar = true;
        }
        elseif ($this->viajeplus < 2) {
            $this->viajeplus++;
            $sePudoPagar = true;
            $tipo = TipoViaje::VIAJE_PLUS;
        }

        $this->ultimoViaje = new Viaje(
                $montoAPagar,
                $colectivo->linea(),
                $this->tiempo->getTiempo(),
                $tipo
            );

        return $sePudoPagar;
    }

    public function recargar($monto) {
        $this->saldo += Tarifas::getCargaEfectiva($monto);
    }
}
