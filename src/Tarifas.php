<?php

namespace TrabajoTarjeta;

class Tarifas {
	const boleto = 14.8;
	const medio_boleto = 7.4;

	public static function getCargaEfectiva($carga){
		if ($monto == 10 ||
				$monto == 20 ||
				$monto == 30 ||
				$monto == 50 ||
				$monto == 100){
			return $monto;
		}
		if ($monto == 1119.9) {
			return 1300.0;
		}
		if ($monto == 2114.11) {
			return 2600.0;
		}
		throw new InvalidArgumentException("Monto a cargar no valido", 1);
	}
}
