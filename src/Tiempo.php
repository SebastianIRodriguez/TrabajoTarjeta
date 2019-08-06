<?php

namespace TrabajoTarjeta;

class Tiempo implements TiempoInterface {

    public function reciente() {

      return time();
    } 

}
