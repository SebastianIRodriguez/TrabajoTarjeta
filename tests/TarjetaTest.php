<?php
namespace TrabajoTarjeta;
use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */

    public function testCargaSaldo() {
        $tiempo  = new TiempoFalso(0);
        $tarjeta = new Tarjeta($tiempo);

        $valoresAProbar = array(10,20,30,60,110,210,802.08,1986.25);

        foreach ($valoresAProbar as $valor) {
          $tarjeta = new Tarjeta($tiempo);
          $this->assertEquals($tarjeta->obtenerSaldo(), $valor);
        }
    }


    /**
     *testeamos transbordos para tarjetas de tipo franquicia normal
     */
     public function testTransbordoTarjetaNormalDiaSemanal() {

        $tiempo = new TiempoFalso(10);
        $tarjeta = new Tarjeta($tiempo);
        $colectivo = new Colectivo("144 n", "mixta", 20);
        $colectivo2 = new Colectivo("145", "semtur", 50);

        $tarjeta->recargar(100);
        $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos un viaje

        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::boleto); //verficamos que el saldo se haya restado correctamente

        $tiempo->Avanzar(59 * 60); //avanzamos el tiempo 59 minutos por lo que debemos poder pagar transbordo

        $this->assertEquals($tarjeta->tiempoTransbordo(), 60 * 60); //por defecto nos encontramos en un dia de semana, por lo que debemos tener solo 60 minutos para el transbordo


        $this->assertTrue($tarjeta->esTransbordo()); //verificamos que el proximo viaje a realizar sea un transbordo

        $this->assertTrue($tarjeta->pagar($colectivo2)); //volvemos a pagar un viaje pero en otro colectivo, para que se puede efectivizar el transbordo

        $this->assertTrue($tarjeta->devolverUltimoTransbordo()); //verificamos que el ultimo viaje haya sido un transbordo

        $this->assertEquals($tarjeta->devolverMontoTransbordo(), Tarifas::transbordo); //verificamos que el monto del transbordo sea el 33% del monto normal


        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::boleto - Tarifas::transbordo); //verificamos que el saldo se haya restado correctamente

        $tiempo2  = new TiempoFalso(10);
        $tarjeta2 = new Tarjeta($tiempo2);
        $this->assertTrue($tarjeta2->pagar($colectivo)); //pagamos un plus
        $tiempo->Avanzar(60 * 30); //avanzamos el tiempo media hora
        $tarjeta2->recargar(100);
        $this->assertFalse($tarjeta2->esTransbordo());
        $this->assertTrue($tarjeta2->usoplus());

        $this->assertEquals($tarjeta2->CantidadPlus(), 1);
        $this->assertTrue($tarjeta2->pagar($colectivo2)); //como nuestro ultimo viaje fue plus, no debemos poder pagar transbordo

        $this->assertFalse($tarjeta2->devolverUltimoTransbordo()); //verificamos que el viaje no haya sido transbordo
        $this->assertEquals($tarjeta2->obtenerSaldo(), 100 - Tarifas::boleto - Tarifas::transbordo);

        $tiempo2->Avanzar(60 * 30); //avanzamos media hora el tiempo


        $this->assertFalse($tarjeta2->usoplus());
        $this->assertTrue($tiempo2->reciente() - $tarjeta2->DevolverUltimoTiempo() < 60 * 60);
        $this->assertTrue($tarjeta2->pagar($colectivo)); //pagamos un transbordo
        $this->assertTrue($tarjeta2->devolverUltimoTransbordo());
        $this->assertEquals($tarjeta2->obtenerSaldo(), 100 - Tarifas::boleto - Tarifas::transbordo * 2); //verificamos que efectivamente el viaje haya sido un transbordo

        $tiempo2->Avanzar(60 * 30); //avanzamos media hora el tiempo

        $this->assertFalse($tarjeta2->esTransbordo());
        $this->assertTrue($tarjeta2->pagar($colectivo2)); //pagamos otro viaje, que no debe ser transbordo dado que nuestro ultimo viaje fue transbordo.

        $this->assertFalse($tarjeta2->devolverUltimoTransbordo());
        $this->assertEquals($tarjeta2->obtenerSaldo(), 100 - Tarifas::boleto * 2 - Tarifas::transbordo * 2); //verificamos lo anteriormente dicho

        $tarjeta3 = new Tarjeta($tiempo2);
        $tarjeta3->recargar(100);

        $this->assertTrue($tarjeta3->pagar($colectivo)); //pagamos
        $this->assertFalse($tarjeta3->ColectivosIguales()); //si todo sale segun lo programado, la funcion colectivos iguales deberia ser FALSE
        $tiempo2->Avanzar(59 * 60); //avanzamos el tiempo 59 minutos
        $this->assertEquals($tarjeta3->obtenerSaldo(), 100 - Tarifas::boleto); //verificamos el saldo

        $this->assertEquals($tarjeta3->devolverUltimoColectivo()->linea(), $colectivo->linea());
        $this->assertTrue($tarjeta3->pagar($colectivo)); //como estamos en el mismo colectivo, no debemos poder pagar transbordo

        $this->assertFalse($tarjeta3->devolverUltimoTransbordo()); //verificamos que el viaje NO sea transbordo
        $this->assertEquals($tarjeta3->obtenerSaldo(), 100 - Tarifas::boleto * 2);
        //verificamos que el saldo se haya restado correctamente

        $tiempo2->Avanzar(60 * 90); //avanzamos el tiempo 1 hora y media

        $this->assertTrue($tarjeta3->pagar($colectivo2)); //pagamos, y como paso mas de una hora no podremos usar transbordo
        $this->assertFalse($tarjeta3->devolverUltimoTransbordo());
        $this->assertEquals($tarjeta3->obtenerSaldo(), 100 - Tarifas::boleto * 3); //verificamos que el saldo este bien restado


    }
    /**
     *testeamos transbordos para tarjetas de tipo franquicia normal en dia no semanales
     */

    public function testTransbordoTarjetaDiaNoSemanal() {
        $tiempo     = new TiempoFalso(10);
        $tarjeta    = new Tarjeta($tiempo);
        $colectivo  = new Colectivo("144", "semtur", 30);
        $colectivo2 = new Colectivo("145", "mixta", 54);

        $tiempo->setTrue($tiempo);
        $this->assertEquals($tarjeta->tiempoTransbordo(), 90 * 60); //activamos los transbordos de 90 minutos

        $tarjeta->recargar(100);
        $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos un viaje

        $tiempo->Avanzar(89 * 60); //avanzamos el tiempo 89 minutos

        $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un viaje
        $this->assertTrue($tarjeta->devolverUltimoTransbordo()); //verificamos que el viaje sea transbordo
        $this->assertEquals($tarjeta->obtenerSaldo(), (100 - Tarifas::boleto - Tarifas::boleto * 0.33)); //verificamos que se reste el saldo correctamente

        $tiempo->Avanzar(91 * 60); //avanzamos el tiempo 91 por lo que el proximo viaje no debe ser transbordo

        $this->assertTrue($tarjeta->pagar($colectivo));
        $this->assertFalse($tarjeta->devolverUltimoTransbordo()); //pagamos y verificamos que el viaje no sea transbordo

    }

    /**
     * Testeamos los transbordos para tarjetas especiales
     */
    public function testTransbordoEnTarjetasEspeciales() {
        $tiempo      = new TiempoFalso(10);
        $medioBoleto = new MedioBoletoUniversitario($tiempo);
        $colectivo   = new Colectivo("144", "semtur", 30);
        $colectivo2  = new Colectivo("145", "mixta", 54);

        $medioBoleto->recargar(100);

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo)); //pagamos un viaje normal
        $this->assertEquals($medioBoleto->obtenerSaldo(), 100 - Tarifas::medio_boleto);

        $this->assertTrue($tiempo->esDiaSemana()); //por defecto es dia de semana

        $tiempo->Avanzar(59 * 60); //avanzamos 59 minutos el tiempo

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo2)); //pagamos un transbordo
        $this->assertEquals($medioBoleto->DevolverCantidadBoletos(), 1);
        $this->assertTrue($medioBoleto->devolverUltimoTransbordo());
        $this->assertEquals($medioBoleto->obtenerSaldo(), 100 - Tarifas::medio_boleto - Tarifas::transbordo);

        $tiempo->setTrue(); //cargamos los transbordos de 90 minutos

        $this->assertEquals($medioBoleto->tiempoTransbordo(), 90 * 60); //verificamos que los transbordos sean de 90 minutos, es decir los transborods que no ocurren en un dia semanal

        $tiempo->Avanzar(91 * 60); //avanzamos 91 minutos el tiempo

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo)); //pagamos un viaje normal
        $this->assertFalse($medioBoleto->devolverUltimoTransbordo()); //verificamos que el viaje no sea transbordo
        $this->assertEquals($medioBoleto->obtenerSaldo(), 100 - Tarifas::medio_boleto * 2  - Tarifas::transbordo);

        $this->assertEquals($medioBoleto->DevolverCantidadBoletos(),2);

        //ahora nuestros viajes valen 14.8 dado que usamos los 2 boletos a mitad de precio
          $this->assertEquals($medioBoleto->CambioMonto(), Tarifas::boleto);//lo verificamos (la funcion cambio monto nos devuelve el monto a pagar con nuestra tarjeta)

          //el transbordo ahora debe ser el 33% de 14.8 que es el precio del viaje actualmente. Vamos a verificar que esto sea así

        $this->assertEquals($medioBoleto->monto, Tarifas::boleto);
        $this->assertEquals($medioBoleto->devolverMontoTransbordo(), Tarifas::boleto);

        $tiempo->Avanzar(89 * 60); //avanzamos el tiempo 89 minutos por lo que hay transbordo

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo2));
        $this->assertTrue($medioBoleto->devolverUltimoTransbordo());
        $this->assertEquals($medioBoleto->obtenerSaldo(), 100 - Tarifas::medio_boleto * 3  - Tarifas::transbordo);

        $tiempo->Avanzar(30 * 60); //avanzamos media hora el tiempo. No hay transbordo dado que nuestro ultimo viaje si lo fue

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo2)); //pagamos un viaje.

        $tiempo->Avanzar(60 * 60); //avanzamos una hora el tiempo por lo que hay transbordo

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo)); //pagamos el transbordo
        $this->assertTrue($medioBoleto->devolverUltimoTransbordo());
        $this->assertEquals($medioBoleto->obtenerSaldo(), 100 - Tarifas::medio_boleto * 4 - Tarifas::transbordo * 2); //verificamos que el saldo se haya restado correctamente. 58.19 es el resultado de restarle 14,8*0.33 (es decir el valor del transbordo) a 63.074 (que era el saldo que teniamos antes de pagar)

    }

    /**
     *testeamos la funcion que nos devuelve la cantidad de dinero realizada en nuestro ultimo viaje
     */
    public function testUltimoPago() {
        $colectivo = new Colectivo("144 n", "mixta", 20);
        $tiempo1   = new TiempoFalso(10);
        $tarjeta   = new Tarjeta($tiempo1);
        $tarjeta->recargar(100);
        $this->assertTrue($tarjeta->pagar($colectivo));
        $this->assertEquals($tarjeta->devolverUltimoPago(), Tarifas::boleto);
        //creamos una tarjeta y pagamos un viaje normal; verificamos que el ultimo pago sea 14.8(viaje normal)

        $tarjetaPlus = new Tarjeta($tiempo1);
        $tarjetaPlus->recargar(10);
        $this->assertTrue($tarjetaPlus->pagar($colectivo));
        $this->assertTrue($tarjetaPlus->usoplus());
        //creamos una nueva tarjeta y le usamos un viaje plus.

        $tarjetaPlus->recargar(100);
        $this->assertTrue($tarjetaPlus->pagar($colectivo));
        $this->assertEquals($tarjetaPlus->devolverUltimoPago(), Tarifas::boleto * 2);
        //cargamos mas saldo y volvemos a pagar. Como usamos un viaje plus, el pasaje debería salir el doble, dado que adeudamos un plus
        $this->assertEquals($tarjetaPlus->CantidadPlus(), 0);
        //verificamos que ahora no adeudemos ningun plus

        $colectivo2 = new Colectivo ("23","semtur",31);
        $medioBoleto = new MedioBoletoUniversitario($tiempo1);
        $medioBoleto->recargar(100);

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo));//pagamos
        $tiempo1->Avanzar(360);//avanzamos 6 minutos
        //como pasaron 6 minutos debe haber transbordo

        $this->assertTrue($medioBoleto->pagoMedioBoleto($colectivo2));//pagamos un transbordo
        $this->assertTrue($medioBoleto->devolverUltimoTransbordo());//verificamos que el viaje sea transbordo
        $this->assertEquals($medioBoleto->devolverUltimoPago(), Tarifas::transbordo);

    }

    /**
     *testeo que sirve para probar que no podemos cargar nuestra tarjeta si usamos un monto invalido
     */
    public function testCargaSaldoInvalido() {
        $tiempo1 = new TiempoFalso(0);
        $tarjeta = new Tarjeta($tiempo1);

        $this->expectException(InvalidArgumentException::class);
        $tarjeta->recargar(15);

        $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     *testemos que cuando pagamos con franquicia completa nos devuelvan un boleto
     */
    public function testFranquiciaCompleta() {
        $tiempo2    = new TiempoFalso(0);
        $colectivo  = new Colectivo("134", "mixta", 30);
        $franquicia = new FranquiciaCompleta($tiempo2);

        $this->assertEquals($franquicia->obtenerSaldo(), 0.0);
        $boleto = $colectivo->pagarCon($franquicia);
        $this->assertEquals(get_class($boleto), "TrabajoTarjeta\Boleto");
        //verificamos que al pagar nos devuelvan un boleto

    }

    /**
     *Verificamos que cuando useamos un medio boleto el viaje valga la mitad del normal
     */
    public function testMedioBoleto() {
        $tiempo3   = new TiempoFalso(0);
        $colectivo = new Colectivo("134", "mixta", 30);
        $medio     = new MedioBoleto($tiempo3);
        $medio->recargar(20);
        $colectivo->pagarCon($medio);

        $this->assertEquals($medio->obtenerSaldo(), 12.6);
    }

    /**
     *Testeo de viajes plus,comprobamos que no podamos viajar debiendo 2 plus
     */
    public function testViajePlus() {
        $tiempo4   = new TiempoFalso(0);
        $colectivo = new Colectivo("134", "mixta", 30);
        $tarjeta   = new Tarjeta($tiempo4);
        $tarjeta->recargar(10);

        //como la tarjeta solo tiene $10 de carga, cada vez que se invoque a la funcion pagarCon se debe incrementar en 1 la cantidad de viajes plus
        $colectivo->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->CantidadPlus(),1);
        $colectivo->pagarCon($tarjeta);
        $this->assertEquals($tarjeta->CantidadPlus(),2);//verificamos que los plus se sumen correctamente

        $this->assertFalse($colectivo->pagarCon($tarjeta));

        //si los viajes plus funcionan correctamente, cuando querramos usar mas de 2 viajes plus la funcion pagarCon() debe retornar FALSE. En caso de que se retorne el FALSE, se verifica que solamente se pueden usar 2 viajes plus //
        $tarjeta->recargar(100);
    }


    /**
     *Este test se encarga de asegurarse que cuando debemos un viaje
     *plus y pagamos, estos se nos cobren
     */
    public function testSaldoPlus() {
        $tiempo5   = new TiempoFalso(10);
        $colectivo = new Colectivo("134", "mixta", 30);
        $tarjeta   = new Tarjeta($tiempo5);
        $tarjeta->recargar(10); //creamos 2 tarjetas y le cargamos 10 pesos a cada una
        $tarjeta2 = new Tarjeta($tiempo5);
        $tarjeta2->recargar(10);

        $this->assertEquals($tarjeta->obtenerSaldo(), 10);
        $this->assertEquals($tarjeta2->obtenerSaldo(), 10); //veficicamos que las tarjetas de recargen correctamente


        $colectivo->pagarCon($tarjeta); // a tarjeta le gastamos 1 plus

        $colectivo->pagarCon($tarjeta2);
        $colectivo->pagarCon($tarjeta2); //a tarjeta2 le gastamos 2 plus

        $this->assertEquals($tarjeta->CantidadPlus(), 1); //verificamos que se hayan sumado los plus correctamente
        $this->assertEquals($tarjeta2->CantidadPlus(), 2);

        $tarjeta->recargar(100); //recargamos 100 pesos a ambas tarjetas

        $tarjeta2->recargar(100);

        $this->assertEquals($tarjeta->obtenerSaldo(), 110);
        $this->assertEquals($tarjeta2->obtenerSaldo(), 110); //verificamos que el saldo de haya sumado correctamente

        $this->assertTrue($tarjeta->usoplus());
        $this->assertNotEquals($tarjeta->DevolverUltimoTiempo(), null);

        $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos un viaje nuevo, por lo que se nos debe restar el dinero de los viajes plus. primero nos fijamos que hayamos pagado correctamente.

        $this->assertFalse($tarjeta->devolverUltimoTransbordo());

        $this->assertEquals($tarjeta->CantidadPlus(), 0); //verificamos que la variable que almacena la cantidad de viajes plus usados se haya reiniciado a 0
        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifa::boleto); //verificamos que el saldo de haya descontado correctamente


        $this->assertTrue($tarjeta2->pagar($colectivo));
        $this->assertEquals($tarjeta2->CantidadPlus(), 0);
        $this->assertEquals($tarjeta2->obtenerSaldo(), 100 - Tarifa::boleto * 2); //realizamos el mismo proceso con la tarjeta 2
    }

    /**
     * Verificamos que cuando usamos una tarjeta de tipo medio boleto
     * tienen que pasar como minimo 5 minutos para poder realizar otro viaje
     * Verificamos que al 3er viaje del dia el monto pase a valer 14.8
     */
    public function testMedioUniversitario() {
        $tiempo7 = new TiempoFalso(100);
        $tarjeta = new MedioBoletoUniversitario($tiempo7);
        $tarjeta->recargar(100); //creamos una tarjeta y le cargamos 100 pesos
        $colectivo = new Colectivo("134", "mixta", 30);

        $this->assertEquals($tarjeta->obtenerSaldo(), 100); // Verificamos que el monto se haya añadido correctamente

        $this->assertEquals($tarjeta->CambioMonto(), Tarifas::medio_boleto);

        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo)); //realizamos un pago

        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::medio_boleto); //verificamos que el saldo de haya restado correctamente;

        $tiempo7->Avanzar(120); //avanzamos el tiempo 2 minutos

        $this->assertEquals($tiempo7->reciente(), 220);

        $this->assertEquals($tarjeta->getTiempo(), 220); //el tiempo se avanzo correctamente



        $this->assertFalse($tarjeta->pagoMedioBoleto($colectivo)); //intentamos pagar otro viaje. como pasaron menos de 5 minutos el resultado de pagar deberia ser false


        $tiempo7->Avanzar(60 * 95); //avanzamos el tiempo 95 minutos

        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo)); // verificamos que se haya podido realizar el pago

        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::medio_boleto * 2); //verificamos que se haya restado correctamente el saldo

        $tiempo7->Avanzar(60 * 65); //avanzamos el tiempo 65 minutos para poder realizar otro viaje

        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo));

        //como este es el 3er viaje que usamos en el dia, se deben restar 14.8 en vez de 7.4. verificamos que esto sea asi.
        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::medio_boleto * 2 + Tarifas::boleto);

        $tiempo7->Avanzar(60 * 60 * 25); //avanzamos el tiempo mas de un dia por lo que ahora por lo que ahora los pasajes deben volver a valer 7.4

        $this->assertFalse($tarjeta->Horas());

        $this->assertEquals($tarjeta->DevolverCantidadBoletos(), 0);

        $this->assertEquals($tarjeta->CambioMonto(), Tarifas::medio_boleto); //verificamos que el pasaje ahora cueste 7.4

        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo)); //pagamos un pasaje

        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::medio_boleto * 3 + Tarifas::boleto); //verificamos que se resten correctamente lso $7.4 del pasaje

        $nuevoTF      = new TiempoFalso(10);
        $tarjetaNueva = new MedioBoletoUniversitario($nuevoTF);

        $tarjetaNueva->recargar(10);//Creamos una nueva tarjeta y le cargamos $10

        $this->assertTrue($tarjetaNueva->pagoMedioBoleto($colectivo)); //pagamos un viaje

        $nuevoTF->Avanzar(360); //avanzamos el tiempo 6 minutos para poder apgar

        $this->assertTrue($tarjetaNueva->pagoMedioBoleto($colectivo)); //pagamos el 1er viaje plus

        $this->assertTrue($tarjetaNueva->usoplus());

        $this->assertEquals($tarjetaNueva->CantidadPlus(), 1); //verificamos que efectivamente adeudemos 1 plus

        $tarjetaNueva->recargar(100);

        $nuevoTF->Avanzar(60 * 60 * 25); //avanzamos el tiempo mas de un dia

        $this->assertTrue($tarjetaNueva->pagoMedioBoleto($colectivo)); //pagamos un viaje nuevo

        $this->assertEquals($tarjetaNueva->DevolverUltimoPago(), Tarifas::boleto + Tarifas::medio_boleto); //verificamos que el ultimo pago haya sido equivalente al medio boleto + el plus adeudado

        $this->assertEquals($tarjetaNueva->obtenerSaldo(), (110 - (Tarifas::boleto + Tarifas::medio_boleto))); //verificamos que se nos haya descontado el viaje plus que adeudabamos


    }

    /**
     *esta funcion se encarga de verificar que no padamos pagar un pasaje cuando adeudemos 2 plus
     *para las tarjetas de tipo medio boleto
     */
    public function pagoNoValido(){
        $colectivo = new Colectivo("134", "mixta", 30);
        $tiempo    = new TiempoFalso(10);
        $tarjeta   = new MedioBoletoUniversitario($tiempo);
        $this->assertEquals($tarjeta->tipotarjeta(), 'medio universitario'); //verificamos que la tarjeta sea del tipo correcto

        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo));
        $tiempo->Avanzar(360); //avanzamos el tiempo 6 minutos para poder pagar
        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo)); //pagamos 2 viajes plus
        $tiempo->Avanzar(60 * 60 * 26); //avanzamos mas de un dia
        $this->assertTrue($tarjeta->Horas());
        $this->assertFalse($tarjeta->pagoMedioBoleto($colectivo)); //como adeudamos 2 plus no debemos poder pagar

    }



    /**
     * En este test vamos a verificar que las tarjeta de tipo medio estudiantil puedan
     * pagar la cantidad de medios boletos que quieran en el dia
     */
    public function pagoMedioEstudiantil() {
        $timpoM = new TiempoFalso(10);
        $medio = new MedioBoleto($tiempoM);
        $colectivo = new Colectivo("145", "semtur", 58);

        $medio->recargar(100);
        $medio->recargar(100);

        $this->assertTrue($medio->pagar($colectivo));
        $tiempo->Avanzar(360); //avanzamos 6 minutos el tiempo para poder pagar
        $this->assertTrue($medio->pagar($colectivo));
        $tiempo->Avanzar(360); //avanzamos 6 minutos el tiempo para poder pagar
        $this->assertTrue($medio->pagar($colectivo));
        $tiempo->Avanzar(360); //avanzamos 6 minutos el tiempo para poder pagar
        $this->assertTrue($medio->pagar($colectivo)); //pagamos 4 boletos

        $this->assertEquals($medio->obtenerSaldo(), 200 - Tarifas::medio_boleto * 4); //verificamos a traves del saldo que
        //todos los viajes hayan sido medio boleto

    }

    /**
     * Este test verifica que el metodo pago medio boleto ande bien en caso de que:
     * - $tarjeta->horas() sea TRUE
     * - Debamos algun plus
     */
    public function testPago2plus() {
        $colectivo = new Colectivo("134", "mixta", 30);
        $colectivo2 = new Colectivo("135", "mixta", 40);
        $tiempo    = new TiempoFalso(10);
        $tarjeta   = new MedioBoletoUniversitario($tiempo);

        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo)); //pagamos un plus
        $this->assertTrue($tarjeta->usoplus()); //verificamos que sea plus

        $tarjeta->recargar(100); //cargamos saldo
        $tiempo->Avanzar(360); //avanzamos 6 minutos el tiempo para poder pagar

        $this->assertTrue($tarjeta->Horas());//verificamos que hayan pasado menos de 24 horas respecto al ultimo pago
        $this->assertTrue($tarjeta->saldoSuficiente()); //verificamos tener el saldo suficiente para pagar
        $this->assertEquals($tarjeta->CantidadPlus(),1);//verificamos que debamos un plus
        $this->assertTrue($tarjeta->pagoMedioBoleto($colectivo)); //pagamos

        $this->assertEquals($tarjeta->CantidadPlus(), 0);
        $this->assertEquals($tarjeta->MostrarPlusDevueltos(), 1);
        $this->assertFalse($tarjeta->usoplus());
        $this->assertEquals($tarjeta->devolverUltimoPago(), Tarifas::boleto + Tarifas::medio_boleto); //verificamos que el pago sea correcto
        $this->assertEquals($tarjeta->obtenerSaldo(), 100 - Tarifas::boleto - Tarifas::medio_boleto);
        /**verificamos que al pagar se nos descuente el medio boleto y el plus adeudado  */
    }

    /**
     *Testeamos que los transbordos funcionen bien cuando es de noche
     */
    public function testTransbordoDeNoche() {
        $colectivo = new Colectivo("134", "mixta", 30);
        $colectivo2 = new Colectivo("135", "mixta", 40);
        $tiempo    = new TiempoFalso(10);
        $tarjeta   = new Tarjeta($tiempo);

        $tarjeta->recargar(100);
        $tiempo->setTrue($tiempo);

        $this->assertTrue($tiempo->devolverEstado());
        $this->assertTrue($tiempo->esDeNoche()); //verificamos que sea de noche

        $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos
        $tiempo->Avanzar(89 * 60); //avanzamos 89 minutos

        $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un transbordo
        $tiempo->Avanzar(360);

        $this->assertTrue($tarjeta->pagar($colectivo2));

        $tiempo->Avanzar(91 * 60); //avanzamos 91 minutos

        $this->assertTrue($tarjeta->pagar($colectivo));
        $this->assertFalse($tarjeta->devolverUltimoTransbordo()); //verificamos que el viaje no sea transbordo
    }

    /**
     *Testeamos que los transbordos funcionen bien cuando es fin de semana
     */
    public function testTransbordoEnFinDeSemana() {
        $colectivo = new Colectivo("134", "mixta", 30);
        $colectivo2 = new Colectivo("135", "mixta", 40);
        $tiempo    = new TiempoFalso(10);
        $tarjeta   = new Tarjeta($tiempo);

        $tarjeta->recargar(100);
        $tiempo->setTrue($tiempo);

        $this->assertTrue($tiempo->devolverEstado());
        $this->assertTrue($tiempo->esFinDeSemana()); //verificamos que sea de noche

        $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos
        $tiempo->Avanzar(89 * 60); //avanzamos 89 minutos

        $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un transbordo
        $tiempo->Avanzar(360);

        $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un viaje normal

        $tiempo->Avanzar(91 * 60); //avanzamos 91 minutos

        $this->assertTrue($tarjeta->pagar($colectivo));
        $this->assertFalse($tarjeta->devolverUltimoTransbordo()); //verificamos que el viaje no sea transbordo
    }

}
