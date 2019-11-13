<?php
namespace TrabajoTarjeta;
use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    /*
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */

    public function testCargaSaldo() {
        $tiempo = new TiempoSimulado(0);

        $valoresAProbar = array(10,20,30,50,100);

        foreach ($valoresAProbar as $valor) {
          $tarjeta = new Tarjeta($tiempo);
          $tarjeta->recargar($valor);
          $this->assertEquals($tarjeta->getSaldo(), $valor);
        }

        $tarjeta1 = new Tarjeta($tiempo);
        $tarjeta1->recargar(1119.9);
        $this->assertEquals($tarjeta1->getSaldo(), 1300);

        $tarjeta2 = new Tarjeta($tiempo);
        $tarjeta2->recargar(2114.11);
        $this->assertEquals($tarjeta2->getSaldo(), 2600);
    }

    /*
     *testeamos transbordos para tarjetas de tipo franquicia normal
     */
     public function testTransbordoTarjetaNormalDiaSemanal() {

        $tiempo = new TiempoSimulado();
        $tarjeta = new Tarjeta($tiempo);
        $colectivo = new Colectivo("144 n", "mixta", 20);
        $colectivo2 = new Colectivo("145", "semtur", 50);

        $tarjeta->recargar(100);
        $tarjeta->pagar($colectivo);

        $tiempo->avanzarMinutos(59);

        $tarjeta->pagar($colectivo2); //volvemos a pagar un viaje pero en otro colectivo, para que se puede efectivizar el transbordo

        $this->assertEquals(TipoViaje::TRANSBORDO, $tarjeta->getUltimoViaje()->getTipo());

        $this->assertEquals($tarjeta->getSaldo(), 100 - Tarifas::boleto - Tarifas::transbordo);





        $tarjeta2 = new Tarjeta($tiempo);

        //Pagamos un plus
        $tarjeta2->pagar($colectivo);

        $tiempo->avanzarMinutos(30);

        $tarjeta2->recargar(100);

        //Como el ultimo fue plus no debemos poder pagar un transbordo
        $tarjeta2->pagar($colectivo2);

        $this->assertEquals(TipoViaje::NORMAL, $tarjeta2->getUltimoViaje()->getTipo()); //verificamos que el viaje no haya sido transbordo
        $this->assertEquals($tarjeta2->getSaldo(), 100 - Tarifas::boleto * 2);

        $tiempo->avanzarMinutos(30);

        $tarjeta2->pagar($colectivo); //pagamos un transbordo

        $this->assertEquals(TipoViaje::TRANSBORDO, $tarjeta2->getUltimoViaje()->getTipo());
        $this->assertEquals($tarjeta2->getSaldo(), 100 - Tarifas::boleto * 2 - Tarifas::transbordo);

        $tiempo->avanzarMinutos(30); //avanzamos media hora el tiempo

        $tarjeta2->pagar($colectivo2); //pagamos otro viaje, que no debe ser transbordo dado que nuestro ultimo viaje fue transbordo.

        $this->assertEquals(TipoViaje::NORMAL, $tarjeta2->getUltimoViaje()->getTipo());
        $this->assertEquals($tarjeta2->getSaldo(), 100 - Tarifas::boleto * 3 - Tarifas::transbordo);




        $tarjeta3 = new Tarjeta($tiempo);
        $tarjeta3->recargar(100);

        $tarjeta3->pagar($colectivo);

        $tiempo->avanzarMinutos(59);

        $tarjeta3->pagar($colectivo);

        $this->assertNotEquals(TipoViaje::TRANSBORDO, $tarjeta3->getUltimoViaje()->getTipo()); //verificamos que el viaje NO sea transbordo
        $this->assertEquals($tarjeta3->getSaldo(), 100 - Tarifas::boleto * 2);
        //verificamos que el saldo se haya restado correctamente

        $tiempo->avanzarMinutos(90);

        $tarjeta3->pagar($colectivo2); //pagamos, y como paso mas de una hora no podremos usar transbordo
        $this->assertNotEquals(TipoViaje::TRANSBORDO, $tarjeta3->getUltimoViaje()->getTipo());;
        $this->assertEquals($tarjeta3->getSaldo(), 100 - Tarifas::boleto * 3); //verificamos que el saldo este bien restado
    }
    /*
     *testeamos transbordos para tarjetas de tipo franquicia normal en dia no semanales
     */

    public function testTransbordoTarjetaDiaNoSemanal() {
        $tiempo = new TiempoSimulado();
        $tarjeta = new Tarjeta($tiempo);
        $colectivo = new Colectivo("144", "semtur", 30);
        $colectivo2 = new Colectivo("145", "mixta", 54);

        $tiempo->setTrue($tiempo);
        $this->assertEquals($tarjeta->tiempoTransbordo(), 90 * 60); //activamos los transbordos de 90 minutos

        $tarjeta->recargar(100);
        $tarjeta->pagar($colectivo);

        $tiempo->avanzarMinutos(89);

        $tarjeta->pagar($colectivo2);
        $this->assertEquals(TipoViaje::TRANSBORDO, $tarjeta->getUltimoViaje()->getTipo());

        $this->assertEquals(100 - Tarifas::boleto - Tarifas::transbordo, $tarjeta->getSaldo());

        $tiempo->avanzarMinutos(121); //avanzamos el tiempo 91 por lo que el proximo viaje no debe ser transbordo

        $tarjeta->pagar($colectivo);
        $this->assertNotEquals(TipoViaje::TRANSBORDO, $tarjeta->getUltimoViaje()->getTipo());
    }

    /*
    * Testeamos los transbordos para tarjetas especiales
    */
   public function testTransbordoEnTarjetasEspeciales() {
       $tiempo = new TiempoSimulado();
       $medioBoleto = new MedioBoletoUniversitario($tiempo);
       $colectivo = new Colectivo("144", "semtur", 30);
       $colectivo2 = new Colectivo("145", "mixta", 54);

       $medioBoleto->recargar(100);

       $medioBoleto->pagar($colectivo);
       $this->assertEquals($medioBoleto->getSaldo(), 100 - Tarifas::medio_boleto);

       $tiempo->avanzarMinutos(59);

       $medioBoleto->pagar($colectivo2);
       $this->assertEquals($medioBoleto->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO);
       $this->assertEquals($medioBoleto->getSaldo(), 100 - Tarifas::medio_boleto - Tarifas::transbordo);

       $tiempo->setTrue(); //cargamos los transbordos de 90 minutos

       $this->assertEquals($medioBoleto->tiempoTransbordo(), 90 * 60); //verificamos que los transbordos sean de 90 minutos, es decir los transbordos que no ocurren en un dia semanal

       $tiempo->avanzarMinutos(121);

       $this->assertEquals($medioBoleto->getMonto(), Tarifas::medio_boleto);

       $this->assertTrue($medioBoleto->pagar($colectivo)); //pagamos un viaje normal
       $this->assertNotEquals($medioBoleto->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO); //verificamos que el viaje no sea transbordo
       $this->assertEquals($medioBoleto->getSaldo(), 100 - Tarifas::medio_boleto * 2);

       //ahora nuestros viajes valen 14.8 dado que usamos los 2 boletos a mitad de precio
         //lo verificamos (la funcion cambio monto nos devuelve el monto a pagar con nuestra tarjeta)

       $tiempo->avanzarMinutos(89); //avanzamos el tiempo 89 minutos por lo que hay transbordo

       $this->assertTrue($medioBoleto->pagar($colectivo2));
       $this->assertEquals($medioBoleto->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO);
       $this->assertEquals($medioBoleto->getSaldo(), 100 - Tarifas::medio_boleto * 2  - Tarifas::transbordo * 2);

       $tiempo->avanzarMinutos(30); //avanzamos media hora el tiempo. No hay transbordo dado que nuestro ultimo viaje si lo fue

       $this->assertTrue($medioBoleto->pagar($colectivo2));
      $this->assertNotEquals($medioBoleto->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO);

       $tiempo->avanzarMinutos(60); //avanzamos una hora el tiempo por lo que hay transbordo

       $this->assertTrue($medioBoleto->pagar($colectivo)); //pagamos el transbordo
       $this->assertEquals($medioBoleto->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO);
       $this->assertEquals($medioBoleto->getSaldo(), 100 - 2 * Tarifas::medio_boleto - Tarifas::boleto - Tarifas::transbordo * 3); //verificamos que el saldo se haya restado correctamente.
   }

   /*
    *testeamos la funcion que nos devuelve la cantidad de dinero realizada en nuestro ultimo viaje
    */
   public function testUltimoPago() {
       $colectivo = new Colectivo("144 n", "mixta", 20);
       $tiempo1   = new TiempoSimulado(10);
       $tarjeta   = new Tarjeta($tiempo1);
       $tarjeta->recargar(100);
       $this->assertTrue($tarjeta->pagar($colectivo));
       $this->assertEquals($tarjeta->getUltimoViaje()->getValor(), Tarifas::boleto);
       //creamos una tarjeta y pagamos un viaje normal; verificamos que el ultimo pago sea 14.8(viaje normal)

       $tarjetaPlus = new Tarjeta($tiempo1);
       $tarjetaPlus->recargar(10);
       $this->assertTrue($tarjetaPlus->pagar($colectivo));
       $this->assertTrue($tarjetaPlus->usoplus());
       //creamos una nueva tarjeta y le usamos un viaje plus.

       $tarjetaPlus->recargar(100);
       $this->assertTrue($tarjetaPlus->pagar($colectivo));
       $this->assertEquals($tarjetaPlus->getUltimoViaje()->getValor(), Tarifas::boleto * 2);
       //cargamos mas saldo y volvemos a pagar. Como usamos un viaje plus, el pasaje debería salir el doble, dado que adeudamos un plus

       $colectivo2 = new Colectivo ("23","semtur",31);
       $medioBoleto = new MedioBoletoUniversitario($tiempo1);
       $medioBoleto->recargar(100);

       $this->assertTrue($medioBoleto->pagar($colectivo));
       $tiempo1->avanzarMinutos(6);
       //como pasaron 6 minutos debe haber transbordo

       $this->assertTrue($medioBoleto->pagar($colectivo2));//pagamos un transbordo
       $this->assertEquals($medioBoleto->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO);//verificamos que el viaje sea transbordo

   }

   /*
    *testeo que sirve para probar que no podemos cargar nuestra tarjeta si usamos un monto invalido
    */
   public function testCargaSaldoInvalido() {
       $tiempo1 = new TiempoSimulado(0);
       $tarjeta = new Tarjeta($tiempo1);

       $tarjeta->recargar(15);

       $this->assertEquals($tarjeta->getSaldo(), 0);
   }

   /*
    *testemos que cuando pagamos con franquicia completa nos devuelvan un boleto
    */
   public function testFranquiciaCompleta() {
       $tiempo2    = new TiempoSimulado(0);
       $colectivo  = new Colectivo("134", "mixta", 30);
       $franquicia = new FranquiciaCompleta($tiempo2);

       $this->assertEquals($franquicia->getSaldo(), 0);
       $boleto = $colectivo->pagarCon($franquicia);
       //verificamos que al pagar nos devuelvan un boleto

   }

   /*
    *Verificamos que cuando useamos un medio boleto el viaje valga la mitad del normal
    */
   public function testMedioBoleto() {
       $tiempo3   = new TiempoSimulado(0);
       $colectivo = new Colectivo("134", "mixta", 30);
       $medio     = new MedioBoleto($tiempo3);
       $medio->recargar(20);
       $colectivo->pagarCon($medio);

       $this->assertEquals($medio->getSaldo(), 20 - Tarifas::medio_boleto);
   }

   /*
    *Testeo de viajes plus,comprobamos que no podamos viajar debiendo 2 plus
    */
   public function testViajePlus() {
       $tiempo4   = new TiempoSimulado();
       $colectivo = new Colectivo("134", "mixta", 30);
       $tarjeta   = new Tarjeta($tiempo4);
       $tarjeta->recargar(10);

       //como la tarjeta solo tiene $10 de carga, cada vez que se invoque a la funcion pagarCon se debe incrementar en 1 la cantidad de viajes plus
       $colectivo->pagarCon($tarjeta);
       $this->assertEquals($tarjeta->getUltimoViaje()->getTipo(), TipoViaje::VIAJE_PLUS);
       $colectivo->pagarCon($tarjeta);
       $this->assertEquals($tarjeta->getUltimoViaje()->getTipo(), TipoViaje::VIAJE_PLUS);

       $this->assertFalse($colectivo->pagarCon($tarjeta));

       //si los viajes plus funcionan correctamente, cuando querramos usar mas de 2 viajes plus la funcion pagarCon() debe retornar FALSE. En caso de que se retorne el FALSE, se verifica que solamente se pueden usar 2 viajes plus //

   }


   /*
    *Este test se encarga de asegurarse que cuando debemos un viaje
    *plus y pagamos, estos se nos cobren
    */
   public function testSaldoPlus() {
       $tiempo5   = new TiempoSimulado(10);
       $colectivo = new Colectivo("134", "mixta", 30);
       $tarjeta   = new Tarjeta($tiempo5);
       $tarjeta->recargar(10); //creamos 2 tarjetas y le cargamos 10 pesos a cada una
       $tarjeta2 = new Tarjeta($tiempo5);
       $tarjeta2->recargar(10);

       $this->assertEquals($tarjeta->getSaldo(), 10);
       $this->assertEquals($tarjeta2->getSaldo(), 10); //veficicamos que las tarjetas de recargen correctamente


       $colectivo->pagarCon($tarjeta); // a tarjeta le gastamos 1 plus

       $colectivo->pagarCon($tarjeta2);
       $colectivo->pagarCon($tarjeta2); //a tarjeta2 le gastamos 2 plus

       $tarjeta->recargar(100); //recargamos 100 pesos a ambas tarjetas

       $tarjeta2->recargar(100);

       $this->assertEquals($tarjeta->getSaldo(), 110);
       $this->assertEquals($tarjeta2->getSaldo(), 110); //verificamos que el saldo de haya sumado correctamente

       $this->assertTrue($tarjeta->usoplus());

       $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos un viaje nuevo, por lo que se nos debe restar el dinero de los viajes plus. primero nos fijamos que hayamos pagado correctamente.

       $this->assertNotEquals($tarjeta->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO);

       $this->assertEquals($tarjeta->getSaldo(), 110 - Tarifas::boleto * 2); //verificamos que el saldo de haya descontado correctamente

       $this->assertTrue($tarjeta2->pagar($colectivo));
       $this->assertEquals($tarjeta2->getSaldo(), 110 - Tarifas::boleto * 3); //realizamos el mismo proceso con la tarjeta 2
   }

   /*
    * Verificamos que cuando usamos una tarjeta de tipo medio boleto
    * tienen que pasar como minimo 5 minutos para poder realizar otro viaje
    * Verificamos que al 3er viaje del dia el monto pase a valer 14.8
    */
   public function testMedioUniversitario() {
       $tiempo7 = new TiempoSimulado(100);
       $tarjeta = new MedioBoletoUniversitario($tiempo7);
       $tarjeta->recargar(100);
       $colectivo = new Colectivo("134", "mixta", 30);

       $this->assertEquals($tarjeta->getMonto(), Tarifas::medio_boleto);

       $this->assertTrue($tarjeta->pagar($colectivo));

       $this->assertEquals($tarjeta->getSaldo(), 100 - Tarifas::medio_boleto); //verificamos que el saldo de haya restado correctamente;

       $tiempo7->avanzarMinutos(2);

       $this->assertFalse($tarjeta->pagar($colectivo)); //intentamos pagar otro viaje. como pasaron menos de 5 minutos el resultado de pagar deberia ser false

       $tiempo7->avanzarMinutos(95);

       $this->assertTrue($tarjeta->pagar($colectivo)); 

       $this->assertEquals($tarjeta->getSaldo(), 100 - Tarifas::medio_boleto * 2); //verificamos que se haya restado correctamente el saldo

       $tiempo7->avanzarMinutos(65);

       $this->assertTrue($tarjeta->pagar($colectivo));

       //como este es el 3er viaje que usamos en el dia, se deben restar 14.8 en vez de 7.4. verificamos que esto sea asi.
       $this->assertEquals($tarjeta->getSaldo(), 100 - 2 * Tarifas::medio_boleto - Tarifas::boleto);

       $tiempo7->avanzarHoras(25); //avanzamos el tiempo mas de un dia por lo que ahora por lo que ahora los pasajes deben volver a valer 7.4
       $this->assertTrue($tarjeta->pagar($colectivo));
       $this->assertEquals($tarjeta->getSaldo(), 100 - Tarifas::medio_boleto * 3 - Tarifas::boleto);

       $nuevoTF      = new TiempoSimulado(10);
       $tarjetaNueva = new MedioBoletoUniversitario($nuevoTF);

       $tarjetaNueva->recargar(20);//Creamos una nueva tarjeta y le cargamos $10

       $this->assertTrue($tarjetaNueva->pagar($colectivo)); //pagamos un viaje

       $nuevoTF->avanzarMinutos(6); //avanzamos el tiempo 6 minutos para poder apgar

       $this->assertTrue($tarjetaNueva->pagar($colectivo)); //pagamos el 1er viaje plus

       $this->assertTrue($tarjetaNueva->usoplus());

       $tarjetaNueva->recargar(100);

       $nuevoTF->avanzarHoras(25); //avanzamos el tiempo mas de un dia

       $this->assertTrue($tarjetaNueva->pagar($colectivo)); //pagamos un viaje nuevo

       $this->assertEquals($tarjetaNueva->getUltimoViaje()->getValor(), Tarifas::boleto + Tarifas::medio_boleto); //verificamos que el ultimo pago haya sido equivalente al medio boleto + el plus adeudado

       $this->assertEquals($tarjetaNueva->getSaldo(), (120 - (Tarifas::boleto + Tarifas::medio_boleto * 2))); //verificamos que se nos haya descontado el viaje plus que adeudabamos
   }

   /*
    *esta funcion se encarga de verificar que no padamos pagar un pasaje cuando adeudemos 2 plus
    *para las tarjetas de tipo medio boleto
    */
   public function pagoNoValido(){
       $colectivo = new Colectivo("134", "mixta", 30);
       $tiempo    = new TiempoSimulado(10);
       $tarjeta   = new MedioBoletoUniversitario($tiempo);
       $this->assertEquals($tarjeta->getTipoTarjeta(), 'medio universitario'); //verificamos que la tarjeta sea del tipo correcto

       $this->assertTrue($tarjeta->pagar($colectivo));
       $tiempo->avanzarMinutos(6); //avanzamos el tiempo 6 minutos para poder pagar
       $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos 2 viajes plus
       $tiempo->avanzarHoras(26); //avanzamos mas de un dia
       $this->assertTrue($tarjeta->Horas());
       $this->assertFalse($tarjeta->pagar($colectivo)); //como adeudamos 2 plus no debemos poder pagar
   }



     /*
    * En este test vamos a verificar que las tarjeta de tipo medio estudiantil puedan
    * pagar la cantidad de medios boletos que quieran en el dia
    */
   public function pagoMedioEstudiantil() {
       $timpoM = new TiempoSimulado(10);
       $medio = new MedioBoleto($tiempoM);
       $colectivo = new Colectivo("145", "semtur", 58);

       $medio->recargar(100);
       $medio->recargar(100);

       $this->assertTrue($medio->pagar($colectivo));
       $tiempo->avanzarMinutos(6); //avanzamos 6 minutos el tiempo para poder pagar
       $this->assertTrue($medio->pagar($colectivo));
       $tiempo->avanzarMinutos(6); //avanzamos 6 minutos el tiempo para poder pagar
       $this->assertTrue($medio->pagar($colectivo));
       $tiempo->avanzarMinutos(6); //avanzamos 6 minutos el tiempo para poder pagar
       $this->assertTrue($medio->pagar($colectivo)); //pagamos 4 boletos

       $this->assertEquals($medio->getSaldo(), 200 - Tarifas::medio_boleto * 4); //verificamos a traves del saldo que
       //todos los viajes hayan sido medio boleto
   }

   /*
    * Este test verifica que el metodo pago medio boleto ande bien en caso de que:
    * - $tarjeta->horas() sea TRUE
    * - Debamos algun plus
    */
   public function testPago2plus() {
       $colectivo = new Colectivo("134", "mixta", 30);
       $colectivo2 = new Colectivo("135", "mixta", 40);
       $tiempo = new TiempoSimulado(10);
       $tarjeta = new MedioBoletoUniversitario($tiempo);

       $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos un plus
       $this->assertTrue($tarjeta->usoplus()); //verificamos que sea plus

       $tarjeta->recargar(100); //cargamos saldo
       $tiempo->avanzarMinutos(6); //avanzamos 6 minutos el tiempo para poder pagar

       $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos

       $this->assertFalse($tarjeta->usoplus());
       $this->assertEquals($tarjeta->getUltimoViaje()->getValor(), Tarifas::boleto + Tarifas::medio_boleto); //verificamos que el pago sea correcto
       $this->assertEquals($tarjeta->getSaldo(), 100 - Tarifas::boleto - Tarifas::medio_boleto);
       /*verificamos que al pagar se nos descuente el medio boleto y el plus adeudado  */
   }

   /*
    *Testeamos que los transbordos funcionen bien cuando es de noche
    */
   public function testTransbordoDeNoche() {
       $colectivo = new Colectivo("134", "mixta", 30);
       $colectivo2 = new Colectivo("135", "mixta", 40);
       $tiempo    = new TiempoSimulado(10);
       $tarjeta   = new Tarjeta($tiempo);

       $tarjeta->recargar(100);
       $tiempo->setTrue($tiempo);

       $this->assertTrue($tiempo->devolverEstado());
       $this->assertTrue($tiempo->esDeNoche()); //verificamos que sea de noche

       $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos
       $tiempo->avanzarMinutos(89); //avanzamos 89 minutos

       $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un transbordo
       $tiempo->avanzarMinutos(6);

       $this->assertTrue($tarjeta->pagar($colectivo2));

       $tiempo->avanzarMinutos(121); //avanzamos 91 minutos

       $this->assertTrue($tarjeta->pagar($colectivo));
       $this->assertNotEquals($tarjeta->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO); //verificamos que el viaje no sea transbordo
   }

   /*
    *Testeamos que los transbordos funcionente bien cuando es fin de semana
    */
   public function testTransbordoEnFinDeSemana() {
       $colectivo = new Colectivo("134", "mixta", 30);
       $colectivo2 = new Colectivo("135", "mixta", 40);
       $tiempo = new TiempoSimulado(10);
       $tarjeta   = new Tarjeta($tiempo);

       $tarjeta->recargar(100);
       $tiempo->setTrue($tiempo);

       $this->assertTrue($tiempo->devolverEstado());
       $this->assertTrue($tiempo->esFinDeSemana()); //verificamos que sea de noche

       $this->assertTrue($tarjeta->pagar($colectivo)); //pagamos
       $tiempo->avanzarMinutos(89); //avanzamos 89 minutos

       $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un transbordo
       $tiempo->avanzarMinutos(6);

       $this->assertTrue($tarjeta->pagar($colectivo2)); //pagamos un viaje normal

       $tiempo->avanzarMinutos(121); //avanzamos 91 minutos

       $this->assertTrue($tarjeta->pagar($colectivo));
       $this->assertNotEquals($tarjeta->getUltimoViaje()->getTipo(),TipoViaje::TRANSBORDO); //verificamos que el viaje no sea transbordo
   }
}
