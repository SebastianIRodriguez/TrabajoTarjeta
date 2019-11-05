<?php

namespace TrabajoTarjeta;

class Tarifas {
	const boleto = 14.8;
	const medio_boleto = 7.4;
	const transbordo = 0;

	const montos = array (10,20,30,50,100,1119.9,2114.11)

	public static function getCargaEfectiva($monto){
		if ($monto==$montos[0]||$monto==$montos[1]||$monto==$montos[2]||$monto==$montos[3]||$monto==$montos[4]){
			return $monto;
		}
		if ($monto == $montos[5]) {
			return 1300.0;
		}
		if ($monto == $montos[6]) {
			return 2600.0;
		}
		throw new InvalidArgumentException("Monto a cargar no valido", 1);
	}
}
