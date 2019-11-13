# TrabajoTarjeta

### Cambios en la implementación:

* Movida logica de Medio Boleto a trait.
* Los nombres de muchas variables se cambiaron por otros mas claros.
* Se removieron metodos de TarjetaInterface que se usaban para testear detalles de implementacion.
* Se hizo un refactoring brutal de la mayoria de los metodos para que fueran mas faciles de entender.
* Se removieron los montos hardcodeados en favor de constantes que representan el valor de cada tipo de boleto.
* Se removieron algunos comentarios y verificaciones de algunos tests que los hacian largos y dificiles de comprender.
* Y aunque fue duro logramos entender parcialmente el funcionamiento del código.
* Se actualizaron las tarifas.
* Se extendio el transbordo durante la noche a 2 horas.
* Se movio la logica de Transbordo a Trait.

### Desafios y observaciones:

* Partimos de un repositorio muy poco conveniente. Tenia codigo muy complejo de comprender y tests demasiado largos.
* Tenia metodos y variables sin uso.
* La clase TarjetaTest.php tiene alrededor de 500 lineas.
* Se rescribio la mayor parte del codigo.
* Probablemente hubiera sido mas rapido empezar de 0.
* Los nombres de metodos y variables eran muy malos. No se entendia su proposito.

### Como se podria mejorar:

* El codigo se podria mejorar aun mas, no le pudimos dedicar suficiente tiempo como para que quedara perfecto.
* Se podria reemplazar a la interfaz TarjetaInterface por una clase abstracta que contenga todas las implementaciones por defecto, en especial de todos los setters y getters.
* Habria que remover todas las lineas en los tests que prueba detalles de implementacion, en vez de comportamiento.
* Habria que remover todos los comentarios en los tests, que con el tiempo quedan desactualizados. El codigo deberia explicarse por si mismo o a lo sumo con comentarios minimos.
