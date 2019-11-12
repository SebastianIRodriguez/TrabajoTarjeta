<?php

namespace TrabajoTarjeta;

class Tarifas {
	const boleto = 14.8;
	const medio_boleto = 7.4;
	const transbordo = 0;

	const montos = array (10,20,30,50,100,1119.9,2114.11);

	public static function getCargaEfectiva($monto): float {
		if ($monto==self::montos[0]||$monto==self::montos[1]||$monto==self::montos[2]||$monto==self::montos[3]||$monto==self::montos[4]){
			return $monto;
		}
		if ($monto == self::montos[5]) {
			return 1300.0;
		}
		if ($monto == self::montos[6]) {
			return 2600.0;
		}
		else {
			return 0;
		}
	}
}
