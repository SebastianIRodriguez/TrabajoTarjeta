<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface {

    public function getTiempo() {

      return time();
    } 

}
