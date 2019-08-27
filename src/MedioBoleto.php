<?php
namespace TrabajoTarjeta;

class MedioBoleto extends MedioBoletoUniversitario {

  public $monto = Tarifas::medio_boleto;  

    public function tipotarjeta() 
    {
        return 'media franquicia estudiantil';
    }


} 
