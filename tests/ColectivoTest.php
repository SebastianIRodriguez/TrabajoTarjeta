<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase {
    
    /**
     *Testeamos las funciones linea(),empresa() y numero()
     */
    public function testAlgoUtil() {
        $coletivo = new Colectivo("144 n", "mixta", 20);
        
        $this->assertEquals($coletivo->linea(), "144 n");
        $this->assertEquals($coletivo->empresa(), "mixta");
        $this->assertEquals($coletivo->numero(), 20);
        
    }
    
    /**
     * Testemos que la funcion pagarCon ande correctamente
     */
    public function testeoPagarCon() {
        $saldoCargado = 20;

        $colectivo = new Colectivo("134", "mixta", 30);
        $tiempo    = new TiempoFalso(10);
        $tarjeta   = new Tarjeta($tiempo);
        
        
        $tarjeta->recargar($saldoCargado);
        $this->assertEquals(get_class($colectivo->pagarCon($tarjeta)), "TrabajoTarjeta\Boleto");
        
        $boleto = $colectivo->pagarCon($tarjeta);
        //pagamos un viaje en almacenamos el boleto en la variable boleto. adeudamos un viaje plus
        $this->assertEquals($tarjeta->getSaldo(), $saldoCargado - Tarifas::boleto);
        $this->assertEquals($tarjeta->CantidadPlus(), 1);
        
        $this->assertTrue($tarjeta->pagar($colectivo));
        //pagamos otro viaje por lo que adeudamos 2 viajes plus.
        $this->assertFalse($colectivo->pagarCon($tarjeta));
        //como debemos 2 viajes plus y no tenemos el saldo suficiente pagarCon debe devoler FALSE como resultado
        
        $tarjeta->recargar(100);
        
        $this->assertEquals(get_class($colectivo->pagarCon($tarjeta)), "TrabajoTarjeta\Boleto");
        
        $this->assertEquals($tarjeta->getValorUltimoPago(), Tarifas::boleto * 3); //pagamos y verificamos que nuestro saldo de haya descontado correctamente 
        
        
        $tarjetaMedioBoleto = new MedioBoleto($tiempo);
        
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto);
        
        $tiempo->avanzarMinutos(6); //avanzamos el tiempo 6 minutos para poder pagar
        
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto); //pagamos 2 plus
        
        $tarjetaMedioBoleto->recargar(100);
        
        $tiempo->avanzarMinutos(6);
        
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto); //volvemos a realizar un pago luego de deber 2 plus
        
        $this->assertEquals($boleto->obtenerValor(), Tarifas::boleto * 2 + Tarifas::medio_boleto); //verificamos que el valor del ultimo viaje sea el correctto
        
        $this->assertFalse($tarjetaMedioBoleto->ultimoViajeFueTransbordo());
        $this->assertFalse($tarjetaMedioBoleto->usoplus()); // verificamos que el ultimo viaje no haya sido un viaje plus
        
        $tiempo->avanzarMinutos(59); //avanzamos 59 minutos el tiempo. pero no debe haber transbordo
        //dado que viajamos en la misma linea

        $this->assertFalse($tarjetaMedioBoleto->Horas());

        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto);

        $this->assertEquals($boleto->obtenerTipo(), 'media franquicia estudiantil'); //verificamos que el viaje 
    //sea de tipo medio boleto 

        $colectivo2 = new Colectivo ("156","mixta",10); 
        $tiempo->avanzarMinutos(6);

        $boleto = $colectivo2->pagarCon($tarjetaMedioBoleto); //pagamos un transbordo

        $this->assertEquals($boleto->obtenerTipo(),"TRANSBORDO");//verificamos que el boleto sea transbordo

    }
}