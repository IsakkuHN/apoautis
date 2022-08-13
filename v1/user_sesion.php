<?php
class UserSession{
    public function __construct(){
        session_set_cookie_params(60*60*24*14);
        session_start();
    }

    public function setCurrentUser($user){
      $_SESSION['nombre_usuario'] = $user;
    }

    public function getCurrentUser(){
        if(isset($_SESSION['nombre_usuario'])){
            return $_SESSION['nombre_usuario'];
        }
    }
}


?>