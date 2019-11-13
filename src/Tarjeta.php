<?php
namespace TrabajoTarjeta;
class Tarjeta implements TarjetaInterface {

    use TransbordoTrait;

    private $saldo = 0;
    public $monto = Tarifas::boleto;
    protected $viajeplus = 0;
    protected $ID;
    protected $tipo = 'franquicia normal';
    protected $tiempo;
    protected $ultimoViaje = null;


    public function __construct(TiempoInterface $tiempo) {
        $this->ID = rand(0, 100);
        $this->tiempo = $tiempo;
    }

    public function getUltimoViaje(): ViajeInterface{
        return $this->ultimoViaje;
    }

    public function usoplus() {
        return $this->ultimoViaje->getTipo() == TipoViaje::VIAJE_PLUS;
    }

    public function getSaldo() {
        return $this->saldo;
    }

    public function getId() {
        return $this->ID;
    }

    //indica si tenemos saldo suficiente para pagar un viaje
    protected function saldoSuficiente() {
        return ($this->saldo >= ($this->monto + $this->viajeplus * Tarifas::boleto));
    }

    public function pagar(Colectivo $colectivo) {

        $sePudoPagar = false;
        $montoAPagar = 0.0;
        $tipo = null;

        //Si tengo para pagar
        if ($this->saldoSuficiente()) {

            if ($this->ultimoViaje == NULL) {
                $montoAPagar = $this->monto;
                $tipo = TipoViaje::NORMAL;
            }
            elseif ($this->esTransbordo($colectivo, $this->tiempo, $this->ultimoViaje)) {
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
        else {
          return false;
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
