<?php
require_once "config_db.php";

class DbModelo{
    public $_db;
    public function __construct(){
        $this->_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ( $this->_db->connect_errno ){
            echo "Fallo al conectar a MySQL: ". $this->_db->connect_error;
            return;
        }
        $this->_db->set_charset("utf8");
    }



    public function getExisteUsuario($us){
      $str="SELECT nombre_usuario FROM tbl_usuarios WHERE nombre_usuario='$us'";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      if ($datos) {
        if($datos['nombre_usuario']===$us){
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }
    
    public function getExisteUsuarioActualizar($id,$us){
      $str="SELECT nombre_usuario FROM tbl_usuarios WHERE id_usuario=$id and nombre_usuario=$us";
      $r = $this->_db->query($str);
      if($r){
        $datos = $r->fetch_assoc();
      }else{
        $datos =[];
      }
      return $datos;
    }

    public function getPreguntas($us,$estado_login){
      $str="";
      $estado=$estado_login;
      if($estado_login=="PV"){
        $str="SELECT CONCAT('[',group_concat(CONCAT('[',id_pregunta,',\"',pregunta,'\"]') SEPARATOR ','),']') AS preguntas FROM tbl_preguntas;";
      }else{
        $str="SELECT CONCAT('[',group_concat(CONCAT('[',id_pregunta,',\"',pregunta,'\"]') SEPARATOR ','),']') AS preguntas FROM tbl_preguntas P INNER JOIN tbl_pregunta_usuario PU ON p.id_pregunta= pu.fk_pregunta INNER JOIN tbl_usuarios u ON pu.fk_usuario = u.id_usuario WHERE u.nombre_usuario='$us';";
      }
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      if ($datos) {
          return $datos['preguntas'];
      }else{
        return false;
      }
    }

    public function comprobarToken($b64){
      $data = base64_decode( $b64 );
      $data = json_decode( $data );
      $str="SELECT (UNIX_TIMESTAMP(now())-".$data->time.")/60<(SELECT Valor FROM tbl_parametro WHERE Parametro='MINUTOS_DURACION_TOKEN') AS fechaValida FROM DUAL";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      if($datos['fechaValida']){
        $str="SELECT token='$data->token' AS validToken FROM tbl_usuarios WHERE nombre_usuario='$data->user'";
        $r = $this->_db->query($str);
        $datos = $r->fetch_assoc();
        if($datos['validToken']){
          return '{"correcto":true,"msg":"Token válido"}';
        }else{
          return '{"correcto":false,"msg":"Token inválido"}';
        }
      }else{
        return '{"correcto":false,"msg":"Token inválido"}';
      }
    }

    public function getParamDb($name){
      $str="SELECT Valor AS value_ FROM tbl_parametro WHERE Parametro='$name'";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $param = $datos['value_'];
      return $param;
    }

    public function getEmailUser($user){
      $str="SELECT correo_electronico FROM tbl_usuarios WHERE nombre_usuario='$user'";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $param = $datos['correo_electronico'];
      return $param;
    }


    public function getInfoCorreo($us){
      $str="SELECT SHA1((now())) AS time_ FROM DUAL";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $token = $datos['time_'];
      $str="UPDATE tbl_usuarios SET token='$token' WHERE nombre_usuario='$us'";
      $update=$this->_db->query($str);
      if ($update) {
        $str="SELECT (SELECT Valor FROM tbl_parametro WHERE Parametro='NOMBRE_SISTEMA') AS sis,(SELECT Valor FROM tbl_parametro WHERE Parametro='PATH_SISTEM') AS path_,nombre_usuario,UNIX_TIMESTAMP(now()) AS time_,token FROM tbl_usuarios WHERE nombre_usuario='$us' AND token='$token'";
        $r = $this->_db->query($str);
        $datos = $r->fetch_assoc();
        $access='{"type":"login","process":1,"user":"'.$us.'","time":'.$datos['time_'].',"token":"'.$datos['token'].'"}';
        if ($datos) {
          $password = "";
          $pattern = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
          $max = strlen($pattern)-1;
          for($i = 0; $i < 10; $i++){
            $password .= substr($pattern, mt_rand(0,$max), 1);
          }
          $str="UPDATE tbl_usuarios SET token=null, pass=AES_ENCRYPT('$password','_sis_'),estado_usuario='A',intentos=0,fecha_cambio_pass=now(), estado_pass=0 WHERE nombre_usuario='$us'";
          $update=$this->_db->query($str);
          $message = "<!DOCTYPE html>
			    <html lang='es'>
			    <head>
			      <meta charset='UTF-8'>
			      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
			      <title>Mensaje</title>
			    
			      <style>
			        * {
			          margin: 0;
			          padding: 0;
			          box-sizing: border-box;
			        }
			    
			        .container {
			          max-width: 1000px;
			          width: 90%;
			          margin: 0 auto;
			        }
			        .bg-dark {
			          background: #ffffff;
			          margin-top: 40px;
			          padding: 20px 0;
			        }
			        .alert {
			          font-size: 1.5em;
			          position: relative;
			          padding: .75rem 1.25rem;
			          margin-bottom: 2rem;
			          border: 1px solid transparent;
			          border-radius: .25rem;
			        }
			        .alert-primary {
			          color: #004085;
			          background-color: #cce5ff;
			          border-color: #b8daff;
			        }
			    
			        .img-fluid {
			          max-width: 100%;
			          height: auto;
			        }
			    
			        .mensaje {
			          width: 80%;
			          font-size: 20px;
			          margin: 0 auto 40px;
			          color: #eee;
			        }
			    
			        .texto {
			          margin-top: 20px;
			        }
			    
			        .footer {
			          width: 100%;
			          background: #48494a;
			          text-align: center;
			          color: #ddd;
			          padding: 10px;
			          font-size: 14px;
			        }
			        .footer span {
			          text-decoration: underline;
			        }
			      </style>
			    </head>
			    <body>
			      <div class='container'>
			        <div class='bg-dark'>
			          <div class='alert alert-primary'>
			            <strong>Mensaje de: </strong> 
			            <br>
			            APO-AUTIS
			          </div>
			          <div class='mensaje'>
			          <br>
			            <img class='img-fluid' src='C:\xampp\htdocs\ApoautisApis\fpdf\Apo-autis.jpg'>
			            <br>
						<br>
						<font color='black'>Estimado: $us</font>
						<br>
						<br>
			            <font color='black'>Usted solicitó el restablecimiento de su contraseña para la Asociación Hondureña de Apoyo al Autista APO-AUTIS mediante correo.</font>
			            <br>
						<br>
			            <font color='black'>Puede acceder desde: http://localhost:4200/login</font>
			            <br>
						<br>
			            <font color='red'>Su contraseña es:$password</font>
						<br>
						<br>
						<font color='black'>Si usted no realizo este cambio, pongase en contacto con el administrado del sistema.</font>
						<br>
						<br>
						<font color='black'>Gracias.</font>
						<br>
						<font color='black'>Asociación Hondureña de Apoyo al Autista APO-AUTIS.</font>
			          </div>
			          <div class='footer'>
                    APO-AUTIS
			          </div>
			        </div>
			      </div>
			    </body>
			    </html>";
            return $message;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }

    public function comprobarPregunta($us,$p,$res){
      $str="SELECT Respuesta FROM tbl_pregunta_usuario WHERE fk_usuario=(SELECT id_usuario FROM tbl_usuarios WHERE nombre_usuario='$us') AND fk_pregunta=$p AND Respuesta='$res'";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      if ($datos) {
          return true;
      }else{
        return false;
      }
    }

    public function insertPass($us,$p,$passGenerada){
      if(!$passGenerada==0){
        $str="SELECT nombre_usuario FROM tbl_usuarios WHERE nombre_usuario='$us' AND pass=CAST(AES_ENCRYPT('$passGenerada','_sis_') AS CHAR(10000) CHARACTER SET utf8)";
        $r = $this->_db->query($str);
        $datos = $r->fetch_assoc();
        if($datos){
          $str="UPDATE tbl_usuarios SET token=null, pass=AES_ENCRYPT('$p','_sis_'),estado_usuario='A',intentos=0,fecha_cambio_pass=now(),estado_pass=1, estado_login='L'   WHERE nombre_usuario='$us'";
          $update=$this->_db->query($str);
          if ($update) {
              return true;
          }else{
            return false;
          }
        }
      }else{
        $str="UPDATE tbl_usuarios SET token=null, pass=AES_ENCRYPT('$p','_sis_'),estado_usuario='A',intentos=0,fecha_cambio_pass=now(),estado_pass=1, estado_login='L'  WHERE nombre_usuario='$us'";
        $update=$this->_db->query($str);
        if ($update) {
            return true;
        }else{
          return false;
        }
      }
    }

    public function insertPassPv($pv,$res,$us,$p){
      $str="INSERT INTO tbl_pregunta_usuario(fk_usuario,fk_pregunta,Respuesta) VALUES((SELECT id_usuario FROM apoautis.tbl_usuarios WHERE nombre_usuario='$us'),$pv,'$res')";
      $update1=$this->_db->query($str);
      $str="UPDATE tbl_usuarios SET token=null, pass=AES_ENCRYPT('$p','_sis_'),estado_usuario='A',intentos=0,fecha_cambio_pass=now(),estado_pass=1, estado_login='L'  WHERE nombre_usuario='$us'";
      $update=$this->_db->query($str);
      if ($update && $update1) {
          return true;
      }else{
        return false;
      }
    }


    ////////////////////fUNCION PARA CONSULTAR LOS PERMISOS DE UN ROL QUE TIENE ASIGNADO UN SUARIO////////////////////////////////////////////////  
    public function Permisos_rol_usuario($user){
      $str="SELECT o.Nombre_objeto,p.Permiso_insertar,p.Permiso_eliminar,p.Permiso_actualizar, p.Permiso_consultar, r.Rol, e.Estatus
      FROM tbl_usuarios u 
      INNER JOIN tbl_rol r on u.Cod_rol=r.Cod_rol
      INNER JOIN tbl_permisos p on r.Cod_rol=p.Cod_rol
      INNER JOIN tbl_objetos o on p.Cod_objeto=o.Cod_objeto
      INNER JOIN tbl_estatus e on r.Cod_estatus= e.Cod_estatus
      WHERE u.nombre_usuario='$user'";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $estatus_usuario= $datos['Estatus'];
      $rol_usuario=$datos['Rol'];
      if($estatus_usuario=='ACTIVO'){
        return $datos;
      }else{
        return '{"correcto":false,"alert":"¡Usuario bloqueado o inactivo!"}';

      }
    }

    

    public function get_us_valido($us,$pass){
      $str="SELECT estado_pass FROM tbl_usuarios WHERE nombre_usuario='$us';";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      if($datos['estado_pass']=="0"){
        return '{"login":"Passcorrecto","correcto":true,"alert":"¡Su contraseña fue restablecida correctamente, ahora debe cambiarla!"}';
      }else{
        $str="SELECT id_usuario,nombre_usuario, estado_login FROM tbl_usuarios WHERE nombre_usuario='$us' AND estado_usuario='A'  AND pass=CAST(AES_ENCRYPT('$pass','_sis_') AS CHAR(10000) CHARACTER SET utf8)";
        $r = $this->_db->query($str);
        $datos = $r->fetch_assoc();
        if ($datos) {
        if($datos['estado_login']=="PV"){
          $str="UPDATE tbl_usuarios SET estado_login='L'  WHERE nombre_usuario='$us'";
          $update=$this->_db->query($str);
          return '{"status_login":"PV","correcto":false,"alert":"¡Usuario por primera vez!"}';
        }else{
          $data = array(
            "correcto"=>true,
            "alert"=>"Usuario Bloqueado o Inactivo.",
            "data"=>$datos
          );
            return json_encode($data);
        }
        }else{
          $existe = $this->getExisteUsuario($us);
          if($existe){
            $str="SELECT nombre_usuario, estado_usuario FROM tbl_usuarios WHERE nombre_usuario='$us'";
            $r = $this->_db->query($str);
            $datos = $r->fetch_assoc();
            if($datos['estado_usuario']=="I"){
              return '{"correcto":false,"alert":"¡Usuario bloqueado o inactivo!"}';
            }else{
              $str="SELECT intentos,(SELECT Valor FROM tbl_parametro WHERE Parametro='INTENTOS_LOGIN') AS intentos_login FROM tbl_usuarios WHERE nombre_usuario='$us'";
              $r = $this->_db->query($str);
              $datos = $r->fetch_assoc();
              $intentos=$datos['intentos']+1;
              $intentosLogin=3;
              if($intentos==$intentosLogin){
                $str="UPDATE tbl_usuarios SET estado_usuario='I',intentos=0 WHERE nombre_usuario='$us'";
                $insert=$this->_db->query($str);
                return '{"correcto":false,"alert":"¡Usuario bloqueado o inactivo!"}';
              }else{
                $str="UPDATE tbl_usuarios SET intentos=$intentos WHERE nombre_usuario='$us'";
                $insert=$this->_db->query($str);
                return '{"correcto":false,"alert":"Contraseña incorrecta le quedan '.($intentosLogin-$intentos).' intento(s)"}';
              }
            }
          }else{
            return '{"correcto":false,"alert":"Usuario o contraseña incorrecta."}';
          }
        }

      }
      //mysqli_close($_db);
    }

    public function get_tabla2($idObjeto){
      $str="SELECT config FROM apoautis.config_objetos where id_objeto=$idObjeto;";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $config = $datos['config'];
      $data = base64_decode( $config );
      $data = json_decode( $data );
      return $data;
    }
    
    public function get_tabla($idObjeto){
      try {
        $q="";
        $campos="";
        $where="";
        $str="SELECT config FROM config_objetos where id_objeto=$idObjeto;";
        $r = $this->_db->query($str);
        $datos = $r->fetch_assoc();
        $config = $datos['config'];
        $data = base64_decode( $config );
        $data = json_decode( $data );
        $res="";
        $res.= $data[0]->nombre;
        $res.= $data[0]->filtrosTab[0]->titulo;
        $q.= $data[0]->filtrosTab[0]->q;
        $join= $data[0]->filtrosTab[0]->join;
        for($i=0;$i<count($data[0]->filtrosTab[0]->campos);$i++){
          if($data[0]->filtrosTab[0]->campos[$i]->campoConcat){
            $campos.= $data[0]->filtrosTab[0]->campos[$i]->campoConcat.',';
          }else{
            $campos.= $data[0]->filtrosTab[0]->campos[$i]->campo.',';
          }
          
        }
        $campos=trim($campos, ',');
        $strCount = str_replace("*","COUNT(*) AS t",$q)." WHERE 1 ".$where;
        $q = str_replace("*",$campos,$q)." ".$join." WHERE 1 ".$where;
        //return $q;
        $rData = $this->_db->query($q);
        $rCount= $this->_db->query($strCount);
        $fila=$rCount->fetch_assoc();
        $t_db=$fila['t'];
        $t=$rData->num_rows;
        $a=json_encode($data);
        $cadena_json='{"correcto":true,"total_query":'.$t.',"a":1,"total_db":'.$t_db.',"data":[';
        while ($dataQuery = $rData->fetch_assoc()) {
          $row='[';
          for($i=0;$i<count($data[0]->filtrosTab[0]->campos);$i++){
            if($data[0]->filtrosTab[0]->campos[$i]->template){
              $row.='"'.str_replace("?",$dataQuery[ $data[0]->filtrosTab[0]->campos[$i]->campo ],$data[0]->filtrosTab[0]->campos[$i]->template).'",' ;
            }else{
              $row.='"'.$dataQuery[ $data[0]->filtrosTab[0]->campos[$i]->campo ].'",' ;
            }
          }
          $row = trim($row, ',').']';
          $cadena_json.=$row.',';
        }
        $configTable="";
        $camposConfig='"campos":[';
        for($i=0;$i<count($data[0]->filtrosTab[0]->campos);$i++){
          $px=$data[0]->filtrosTab[0]->campos[$i]->px ? $data[0]->filtrosTab[0]->campos[$i]->px : 0;
          $camposConfig.='{"nombre":"'.$data[0]->filtrosTab[0]->campos[$i]->nombre.'","type": "'.$data[0]->filtrosTab[0]->campos[$i]->type.'","isTemplate": false,"index": '.($i).',"px": '.$px.'},';
        }
        $camposConfig = trim($camposConfig, ',')."]";
        $cadena_json = trim($cadena_json, ',').'],"configTable":{'.$camposConfig.'}}';

        return $cadena_json;
      } catch (\Throwable $th) {
        return $th;
      }
      

    }

    public function dataRow($dataForm){
      $str="SELECT config FROM config_objetos where id_objeto=$dataForm->id;";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $config = $datos['config'];
      $data = base64_decode( $config );
      $data = json_decode( $data );
      for($i=0;$i<count($data[0]->filtrosTab[0]->form);$i++){
        $nameForm="";
        $camposStr="";
        if($data[0]->filtrosTab[0]->form[$i]->nombre==$dataForm->nform){
          for($ii=0;$ii<count($data[0]->filtrosTab[0]->camposRow->campos);$ii++){
            $camposStr.= $data[0]->filtrosTab[0]->camposRow->campos[$ii]->nombre.',';
          }
        }
      }
      $q = str_replace("*",trim($camposStr, ','),$data[0]->filtrosTab[0]->q)." WHERE ".$dataForm->CampoWhere.'="'.$dataForm->where.'"';
      //return $q;
      $rData = $this->_db->query($q);
      $t=$rData->num_rows;
      $dataRow="";
      while ($dataQuery = $rData->fetch_assoc()) {
        for($i=0;$i<count($data[0]->filtrosTab[0]->camposRow->campos);$i++){
          $dataRow.='{"cod":"'.$data[0]->filtrosTab[0]->camposRow->campos[$i]->cod.'","value":"'.$dataQuery[ $data[0]->filtrosTab[0]->camposRow->campos[$i]->nombre ].'"},';
        }
      }
      echo '{"correcto":true,"dataFormRow":['.trim($dataRow, ',').']}';
    }

    public function configForm($dataForm){
      $str="SELECT config FROM config_objetos where id_objeto=$dataForm->id;";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $config = $datos['config'];
      $data = base64_decode( $config );
      $data = json_decode( $data );
      for($i=0;$i<count($data[0]->filtrosTab[0]->form);$i++){
        $nameForm="";
        if($data[0]->filtrosTab[0]->form[$i]->nombre==$dataForm->nform){
          $nameForm.='{"'.$dataForm->nform.'":[';
          $select=""; 
          for($ii=0;$ii<count($data[0]->filtrosTab[0]->form[$i]->campos);$ii++){
            if(isset($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select) && $data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select){
              //$elemenForm=$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod;
              $select.='{"name":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod.'",';
              $r = $this->_db->query($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->query);
              //$select=$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->query;
              if ($r) {
                $options='"values":[';
                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->default){
                  $options.='{"id":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->default->value.'","value":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->default->text.'"},';
                }
                while ($fila = $r->fetch_assoc()) {
                    //$select=$fila;
                    $campoA=$fila[$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->campoA];
                    $campoB=$fila[$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->select->campoB];
                    $options.='{"id":"'.$campoA.'","value":"'.$campoB.'"},';
                }
              }
              $options=trim($options, ',').']';
              $select.=$options.'},';
            }
          }
        }
      }

      return $nameForm.=trim($select, ',').']}';
    }

    public function setTable($dataForm){
      $q="";
      $campos="";
      $where="";
      $str="SELECT config FROM config_objetos where id_objeto=$dataForm->id;";
      //echo $str;
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $config = $datos['config'];
      $data = base64_decode( $config );
      $data = json_decode( $data );
      $table="";
      $campos="";
      /*for($i=0;$i<count($data[0]->filtrosTab[0]->form);$i++){
        if($data[0]->filtrosTab[0]->form[$i]->nombre==$dataForm->nform){
          $table.= $data[0]->filtrosTab[0]->form[$i]->table;
          for($ii=0;$ii<count($data[0]->filtrosTab[0]->form[$i]->campos);$ii++){
            for($j=0;$j<count($dataForm->campos);$j++){
               if($dataForm->campos[$j]->campo==$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod){
                $campos.=$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->campo."='".$dataForm->campos[$j]->value."', ";
               }
            }
          }
        }
      }*/
      $values="";
      $strValidQuery="SELECT EXISTS(*) AS exist";
      $error=false;
      for($i=0;$i<count($data[0]->filtrosTab[0]->form);$i++){
        if($data[0]->filtrosTab[0]->form[$i]->nombre==$dataForm->nform){
          $table.= $data[0]->filtrosTab[0]->form[$i]->table;
          for($ii=0;$ii<count($data[0]->filtrosTab[0]->form[$i]->campos);$ii++){
            for($j=0;$j<count($dataForm->campos);$j++){
               if($dataForm->campos[$j]->campo==$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod){
                $campos.=$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->campo.",";
                // VALIDACIONES 
                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->required){
                  if($dataForm->campos[$j]->value=="" || $dataForm->campos[$j]->value=="0"){
                    $error="¡El campo: ".$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->nombre." esta vacio!";
                    return '{"correcto":false,"error":"'.$error.'","elem":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod.'","errorForm":true}';
                  }
                }

                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->validate!=null){
                  $q=str_replace("?",$dataForm->campos[$j]->value,$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->validate);
                  $q=str_replace("*",$q,$strValidQuery);
                  $r=$this->_db->query($q);
                  $datos = $r->fetch_assoc();
                  $exist = $datos['exist'];
                  if($exist){
                    $error=true;
                    $error=str_replace("?",$dataForm->campos[$j]->value,$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->label_validate);
                    return '{"correcto":false,"error":"'.$error.'","elem":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod.'","errorForm":true}';
                  }
                }
                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->mask){
                  $values.=str_replace("?",$dataForm->campos[$j]->value,$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->mask).",";
                }else{
                  $values.="'".strtoupper($dataForm->campos[$j]->value)."',";
                }
               }
            }
          }
        }
      }
      $str= "INSERT INTO ".$table."(".trim($campos, ',').") VALUES(".trim($values, ',').");";
      //echo $str;
      $insert=$this->_db->query($str);
      if ($insert && !$error) {
        $msg_c="Formulario ingresado con éxito.";
        $msg_co ="success";
        return '{"correcto":true,"msg_co":"'.$msg_co.'"}';
      }else{
        $msg_c="Error al ingresar el formulario.";
        $msg_co ="danger";
        return '{"correcto":false,"msg_co":"'.$msg_co.'","error":'.print_r($this->_db).'}';
      }
    }

    public function editData($dataForm){
      $q="";
      $campos="";
      $where="";
      $str="SELECT config FROM config_objetos where id_objeto=$dataForm->id;";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      $config = $datos['config'];
      $data = base64_decode( $config );
      $data = json_decode( $data );
      $table="";
      $cWhere="";
      $sets="";
      $strValidQuery="SELECT EXISTS(*) AS exist";
      $error=false;
      for($i=0;$i<count($data[0]->filtrosTab[0]->form);$i++){
        if($data[0]->filtrosTab[0]->form[$i]->nombre==$dataForm->nform){
          $table.= $data[0]->filtrosTab[0]->form[$i]->table;
          $cWhere.= $data[0]->filtrosTab[0]->form[$i]->cWhere;
          for($ii=0;$ii<count($data[0]->filtrosTab[0]->form[$i]->campos);$ii++){
            for($j=0;$j<count($dataForm->campos);$j++){
               if($dataForm->campos[$j]->campo==$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod && $dataForm->campos[$j]->campo==$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->edit){ 
                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->requiredEdit){
                  if($dataForm->campos[$j]->value=="" || $dataForm->campos[$j]->value=="0"){
                    $error="¡El campo: ".$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->nombre." esta vacio!";
                    return '{"correcto":false,"error":"'.$error.'","elem":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod.'","errorForm":true}';
                  }
                }
                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->validate){
                  $q=str_replace("?",$dataForm->campos[$j]->value,$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->validate);
                  $q=str_replace("*",$q,$strValidQuery);
                  $r=$this->_db->query($q);
                  $datos = $r->fetch_assoc();
                  $exist = $datos['exist'];
                  if($exist){
                    $error=true;
                    $error=str_replace("?",$dataForm->campos[$j]->value,$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->label_validate);
                    return '{"correcto":false,"error":"'.$error.'","elem":"'.$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->cod.'","errorForm":true}';
                  }
                }
                if($data[0]->filtrosTab[0]->form[$i]->campos[$ii]->mask){
                  $sets.=str_replace("?",$dataForm->campos[$j]->value,$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->mask).",";
                }else{
                  $sets.=$data[0]->filtrosTab[0]->form[$i]->campos[$ii]->campo."='".$dataForm->campos[$j]->value."',";
                }
               }
            }
          }
        }
      }
      $str= "UPDATE ".$table." SET ".trim($sets, ',')." WHERE ".$cWhere."='".$dataForm->where."'";
      //return $str;
      $insert=$this->_db->query($str);
      if ($insert && !$error) {
        $msg_c="Formulario ingresado con éxito.";
        $msg_co ="success";
        return '{"correcto":true,"msg_co":"'.$msg_co.'"}';
      }else{
        $msg_c="Error al ingresar el formulario.";
        $msg_co ="danger";
        return '{"correcto":false,"msg_co":"'.$msg_co.'","error":'.print_r($this->_db).'}';
      }
    }

}
