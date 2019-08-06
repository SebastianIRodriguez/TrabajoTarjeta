<?php
namespace TrabajoTarjeta;
use PHPUnit\Framework\TestCase;

class TiempoTest extends TestCase
{

    public function testSetTrue()
    {
        $tiempo = new TiempoFalso(10); 

        $tarjeta = new Tarjeta($tiempo); 

        $this->assertTrue($tiempo->esDiaSemana()); //por defecto estamos en un dia de semana
        $tiempo->setTrue($tiempo);
        $this->assertTrue($tiempo->devolverEstado()); //al cambiar el estado a true, todos los transbordos abarcaran un tiempo de noventa minutos

        $this->assertFalse($tiempo->esDiaSemana());
        $this->assertFalse($tiempo->esDiaSemana()); //verificamos que no estemos en un dia de semana
        $this->assertEquals($tarjeta->tiempoTransbordo(), 90 * 60); //como es fin de semana el tiempo del transbordo deben ser de 90 minutos

    }

}