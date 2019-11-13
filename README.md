# TrabajoTarjeta

### Problemas de implementación:

* Movida logica de Medio Boleto a trait
* Los nombres de muchas variables se cambiaron por otros mas claros
* Se removieron metodos de TarjetaInterface que se usaban para testear detalles de implementacion
* Se hizo un refactoring brutal de la mayoria de los metodos para que fueran mas faciles de entender
* Se removieron los montos hardcodeados en favor de constantes que representan el valor de cada tipo de boleto
* Se removieron algunos comentarios y verificaciones de algunos tests que los hacian largos y dificiles de comprender
