<?php
namespace TrabajoTarjeta;

class MedioBoleto extends MedioBoletoUniversitario {

  public $monto = 7.4;  

    public function tipotarjeta() 
    {
        return 'media franquicia estudiantil';
    }


} 
