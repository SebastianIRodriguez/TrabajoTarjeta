<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {
    
    public function testSaldoCero() {
        
        $tiempo    = new Tiempo();
        $colectivo = new Colectivo("144 r", "mixta", 712);
        $tarjeta   = new Tarjeta($tiempo);
        $tarjeta->recargar(20);
        $boleto = $colectivo->pagarCon($tarjeta);
        
        $this->assertEquals($boleto->obtenerValor(), $tarjeta->devolverUltimoPago()); //verificamos que el valor del viaje que nos devuelva el boleto sea igual al valor registrado en el ultimo pago de la tarjeta, que en este caso es 0. 
        $this->assertEquals($tarjeta->devolverUltimoPago(), 14.8);
        //$this->assertEquals($boleto->obtenerValor(),14.8); 
        $this->assertEquals($tarjeta->obtenerSaldo(), 5.2); //verificamos que el ultimo pago sea de 14.8 pesos
    }

    /**
     *Testeamos que la funcion fecha ande correctamente
     */
    public function testFecha() {
        
        $tiempo    = new TiempoFalso(0);
        $tarjeta   = new Tarjeta($tiempo);
        $colectivo = new Colectivo("K", "semtur", 30);
        
        $boleto = $colectivo->pagarCon($tarjeta); //creamos una tarjeta. pagamos y almacenamos el boleto resultante en la variable $boleto
        
        $this->assertEquals($boleto->obtenerFecha(), "01-01-1970"); //verificamos que el boleto almacene correctamente la fecha;
        
        
    }

    /**
     * Verificamos que el boleto nos retorne los datos correctos 
     * en base que clase de viaje(viaje plus, franquicia normal, franquicia completa, media franquicia estudiantil,medio universitario) se realizó
     */
    public function testTipoBoleto() {
        
        $tiempo2   = new TiempoFalso(10);
        $colectivo = new Colectivo("144", "mixta", 712);
        $tarjeta   = new Tarjeta($tiempo2); //creamos una tarjeta y un colectivo y pagamos un boleto, que en este caso sera viaje plus porque solo tenemos 10 pesos en la tarjeta
        $boleto    = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "VIAJE PLUS"); //varificamos que el boleto sea de tipo viaje plus
        $tarjeta2 = new Tarjeta($tiempo2);
        $tarjeta2->recargar(20.0);
        $boleto2 = $colectivo->pagarCon($tarjeta2); //creamos una segunda tarjeta y pagamos un viaje normal. verificamos que este viaje sea de tipo franquicia normal
        $this->assertEquals($boleto2->obtenerTipo(), "franquicia normal");
        
        $tarjeta3 = new FranquiciaCompleta($tiempo2);
        $this->assertTrue($tarjeta3->pagar($colectivo)); //verificamos que podamos pagar con la tarjeta
        $tiempo2->Avanzar(60 * 95); //avanzamos el tiempo 95 minutos para que el viaje no sea transbordo
        $boleto3 = $colectivo->pagarCon($tarjeta3);
        $this->assertEquals($boleto3->obtenerTipo(), 'franquicia completa'); //pagamos un boleto que cuya informacion fue almacenada en boleto 3 
        //verificamos que el boleto sea de tipo franquicia completa
        
        $tarjetaMedioBoleto = new MedioBoleto($tiempo2);
        $tarjetaMedioBoleto->recargar(10);
        
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto); //creamos una tarjeta y pagamos. El boleto que obtenemos como resultado lo almacenamos en la variable boleto
        
        $this->assertEquals($boleto->obtenerTipo(), 'media franquicia estudiantil'); //verificamos que $boleto sea del tipo media franquicia estudianti.
        $this->assertEquals($boleto->obtenerValor(), 7.4);
        //verificamos que el valor del pasaje que nos devuelva el boleto sea el correcto
        
        $this->assertEquals($boleto->obtenerColectivo(), '144'); //verificamos que nos devuelvan el colectivo correcto
        
        $tiempo2->Avanzar(360); //avanzamos el tiempo para poder pagar
        
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto); //pagamos un viaje plus
        $this->assertEquals($boleto->obtenerTipo(), 'VIAJE PLUS'); //verificamos que efectivamente el ultimo viaje haya sido un viaje plus;
        $tiempo2->Avanzar(360); //avanzamos el tiempo para poder pagar
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto); //pagamos un segundo viaje plus
        
        $tiempo2->Avanzar(360); //avanzamos el tiempo para poder pagar
        $this->assertFalse($colectivo->pagarCon($tarjetaMedioBoleto)); //como adeudamos 2 plus, no deberiamos poder pagar.
        $tiempo2->Avanzar(360); //avanzamos el tiempo para poder pagar
        $tarjetaMedioBoleto->recargar(100); //cargamos 100$ a la tarjeta
        $boleto = $colectivo->pagarCon($tarjetaMedioBoleto); //pagamos
        $this->assertEquals($tarjetaMedioBoleto->MostrarPlusDevueltos(), 2); //verificamos que hayamos devuelto el viaje plus que usamos
        $this->assertEquals($tarjetaMedioBoleto->CantidadPlus(), 0); //verificamos que ahora no adeudemos ningun plus
        
        
        $colectivoNuevo = new Colectivo("134", "mixta", 30);
        $tiempo4        = new TiempoFalso(10);
        $tarjeta4       = new Tarjeta($tiempo4);
        
        
        $boleto = $colectivo->pagarCon($tarjeta4);
        $boleto = $colectivo->pagarCon($tarjeta4); //pagamos 2 plus
        
        $tarjeta4->recargar(100);
        
        $boleto = $colectivo->pagarCon($tarjeta4); //volvemos a realizar un viaje luego de deber 2 plus
        
        $boletoAuxliar = new Boleto($tarjeta4->devolverUltimoPago(), $colectivo, $tarjeta4, $tarjeta4->tipotarjeta(), "Paga " . (string) $tarjeta4->MostrarPlusDevueltos() . " Viaje Plus"); 
        //este boleto es el boleto que se deberia devolver con el ultimo viaje pagado
        
        $this->assertEquals($boleto, $boletoAuxliar); // verificamos los datos del boleto sean los correctos
        
        $Univer = new MedioBoletoUniversitario($tiempo4); 
        $Univer->recargar(100);//creamos un medio universitario y cargamos $100

        $boleto = $colectivo->pagarCon($Univer);

        $this->assertEquals($boleto->obtenerTipo(),'medio universitario');//verificamos que el tipo del boleto sea medio universitario
        
    }
    
    /**
     * Testeamos que cuando paguemos un transbordo el boleto sea de tipo transbordo
     */
    public function testBoletoTransbordo() {
        $colectivo = new Colectivo("134", "mixta", 30);
        $colectivo2 = new Colectivo("154", "semtur", 89);
        $tiempo    = new TiempoFalso(10);
        $tarjeta   = new Tarjeta($tiempo);
        
        $tiempo->setTrue($tiempo); //seteamos los transbordos de 90 minutos
        $tarjeta->recargar(100); //cargamos saldo
        $boleto = ($colectivo->pagarCon($tarjeta)); //pagamos un viaje y lo guardamos en boleto
        
        $tiempo->Avanzar(89 * 60); //avanzamos el tiempo 89 minutos
    
        //no debe haber transbordo dado que vamos a viajar en la misma linea
        $boleto = $colectivo->pagarCon($tarjeta); //pagamos el boleto y lo guardamos en boleto

        $this->assertFalse($tarjeta->devolverUltimoTransbordo());
        
        $boletoAImprimir = new Boleto($tarjeta->devolverUltimoPago(), $colectivo, $tarjeta, "franquicia normal", " ");
        //estos datos debe contener el boleto que nos dieron al realizar el ultimo pago

        $this->assertEquals($boleto, $boletoAImprimir);

        $tiempo->Avanzar(89 * 60); //avanzamos el tiempo 89 minutos

        $boleto = $colectivo2->pagarCon($tarjeta); //pagamos un transbordo

        $this->assertTrue($tarjeta->devolverUltimoTransbordo());
        $this->assertEquals($boleto->obtenerTipo(), "TRANSBORDO"); //verificamos que el boleto sea transbordo
    }
    
}



