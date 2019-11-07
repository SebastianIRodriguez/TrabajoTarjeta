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
    protected $ultimoplus = false;
    protected $pago = 0;
    protected $plusdevuelto = 0;
    protected $ultimoTiempo = null;
    protected $ultimoTransbordo = false;
    protected $ultimoColectivo = null;
    protected $iguales = false;


    public function __construct(TiempoInterface $tiempo) {
        $this->saldo     = 0.0;
        $this->viajeplus = 0;
        $this->ID        = rand(0, 100);
        $this->ultboleto = null;
        $this->tiempo    = $tiempo;
    }


    public function getTiempo() {
        return $this->tiempo->reciente();
    }

    public function MostrarPlusDevueltos() {
        return $this->plusdevuelto;
    }

    public function getTiempoUltimoViaje() {
        return $this->ultimoTiempo;
    }

    public function reiniciarPlusDevueltos() {
        $this->plusdevuelto = 0;
    }

    public function usoplus() {
        return $this->ultimoplus;
    }

    public function ultimopago() {
        if ($this->ultimoViajeFueTransbordo()) {
          $this->pago = Tarifas::transbordo;
        }
        else {
          $this->pago = $this->monto + Tarifas::boleto * $this->MostrarPlusDevueltos();
        }
    }

    public function getValorUltimoPago() {
        return $this->pago;
    }

    public function getTipoTarjeta() {
        return $this->tipo;
    }

    //devuelve la cantidad de viajes plus que adeudamos
    public function CantidadPlus() {
        return $this->viajeplus;
    }

    public function IncrementoPlus() {
        $this->viajeplus += 1;
    }

    public function RestarPlus() {
        $this->viajeplus = 0;
    }

    //indica si tenemos saldo suficiente para pagar un viaje
    public function saldoSuficiente() {
        return ($this->getSaldo() >= ($this->monto + $this->CantidadPlus() * Tarifas::boleto));
    }

    public function getSaldo() {
        return $this->saldo;
    }

    public function ultimoViajeFueTransbordo() {
        return $this->ultimoTransbordo;
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
            $this->tiempo->reciente() - $this->getTiempoUltimoViaje() < $this->tiempoTransbordo());
    }

    public function restarSaldo() {
        if ($this->getTiempoUltimoViaje() == NULL) {

            $this->saldo -= $this->monto;
            $this->viajeplus = 0;
            $this->ultimoTransbordo = FALSE;
        }
        elseif ($this->esTransbordo()) {

            $this->saldo -= Tarifas::transbordo;
            $this->ultimoTransbordo = TRUE;
        }
        else {
            $this->saldo -= ($this->monto + $this->CantidadPlus() * Tarifas::boleto);
            $this->viajeplus = 0;
            $this->ultimoTransbordo = FALSE;
        }
    }

    public function getId() {
        return $this->ID;
    }

    public function guardarUltimoBoleto($boleto) {
        $this->ultboleto = $boleto;
    }

    public function getUltimoColectivo() {
        return $this->ultimoColectivo;
    }

    public function ColectivosIguales() {
        return $this->iguales;
    }


    public function pagar(Colectivo $colectivo) {

        $this->iguales = (
            ($this->getTiempoUltimoViaje() != null) &&
            ($colectivo->linea() == $this->getUltimoColectivo()->linea()));

        if ($this->saldoSuficiente()) {

            if ($this->usoplus() == FALSE) {
                $this->restarSaldo();
                $this->ultimopago();
                $this->plusdevuelto    = 0;
            }
            else {
                $this->plusdevuelto = $this->CantidadPlus();
                $this->restarSaldo();
                $this->ultimopago();
                $this->RestarPlus();
            }
            
            $this->ultimoplus = false;
            $this->ultimoTiempo = $this->tiempo->reciente();
            $this->ultimoColectivo = $colectivo;

            return true;
        }
        elseif ($this->CantidadPlus() < 2) {
            $this->plusdevuelto = 0;
            $this->ultimoplus   = true;
            $this->IncrementoPlus();
            $this->ultimoTiempo    = $this->tiempo->reciente();
            $this->ultimoColectivo = $colectivo;
            return true;
        }
        return false;
    }

    public function recargar($monto) {
        $this->saldo += Tarifas::getCargaEfectiva($monto);
    }
}
