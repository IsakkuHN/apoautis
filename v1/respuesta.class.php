<?php
class Respuesta{
    var $code;
    var $description;

    function __construct($code,$description){
    $this->code = $code;
    $this->description = $description;
 }

}
