<?php
require_once "config_db.php";
class Response{
    public $code;
    public $descripcion;
    public $existeUsuario;
    public $correoRecuperacion;
    public $preguntas;
    public $PreguntaSeguridad;
    public $passEstablecido;

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }

    public function getExisteUsuario(){
		return $this->existeUsuario;
	}

	public function setExisteUsuario($existeUsuario){
		$this->existeUsuario = $existeUsuario;
	}

    public function getcorreoRecuperacion(){
		return $this->correoRecuperacion;
	}

	public function setcorreoRecuperacion($correoRecuperacion){
		$this->correoRecuperacion = $correoRecuperacion;
	}

    public function getPreguntas(){
		return $this->preguntas;
	}

	public function setPreguntas($preguntas){
		$this->preguntas = $preguntas;
	}

    public function getPreguntaSeguridad(){
		return $this->PreguntaSeguridad;
	}

	public function setPreguntaSeguridad($PreguntaSeguridad){
		$this->PreguntaSeguridad = $PreguntaSeguridad;
	}

    public function getPassEstablecido(){
		return $this->passEstablecido;
	}

	public function setPassEstablecido($passEstablecido){
		$this->passEstablecido = $passEstablecido;
	}
}
?>