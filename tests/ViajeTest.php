<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ViajeTest extends TestCase {
    
    public function testAlmacenaInfoCorrectamente() {
        $viaje = new Viaje(50.0, "122", 54, TipoViaje::TRANSBORDO);

        $this->assertEquals(50.0, $viaje->getValor());
        $this->assertEquals("122", $viaje->getLinea());
        $this->assertEquals(54, $viaje->getTiempo());
        $this->assertEquals(TipoViaje::TRANSBORDO, $viaje->getTipo());
    }
}



