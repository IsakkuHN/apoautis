<?php
//header('Content-Type: application/json');
require_once "config_db.php";
class DbModelo{
  public $_db;
  public function __construct(){
      $this->_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      if ( $this->_db->connect_errno ){
          echo "Fallo al conectar a MySQL: ". $this->_db->connect_error;
          return;
      }
      $this->_db->set_charset("utf8");
  }
  public function set_guardar_imagen($emp,$user){
    $str="INSERT INTO fotos_subidas(n_emp,usuario) VALUES('$emp','$user')";
    $r = $this->_db->query($str);
    return $r;
  }
  public function desencriptar($cadena){
    $param=str_replace("*",'+',trim($cadena));
    $str="SELECT AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') AS r;";
    $r = $this->_db->query($str);
    $datos = $r->fetch_assoc();
    //echo $cadena;
          //echo $datos['r'];
    return $datos['r'];
  }
  public function reset_pass($data){
    function desencriptar($cadena){
           $__K_K='__K&K';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
           $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($__K_K), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($__K_K))), "\0");
          return $decrypted;  //Devuelve el string desencriptado
    }
    $user=json_decode(desencriptar($data[0]));
    if ($user[0]!="" && ($data[1]===$data[2]) ) {
      if ($data[1]===$data[3]) {
        $msg='{"msg":"Debe establecer una contaseña diferente a la que fue brindada.","correcto":false,"co_error":"danger"}';
      }else{
        $user=$user[0];
        $pass=$data[1];
        //echo $pass_old;
        $str="SELECT reset_pass_us('$pass', '$user') AS r;";
        $r = $this->_db->query($str); // HACER LA CONSULTA
        $datos = $r->fetch_assoc();
        if ($datos['r']) {
         $msg='{"msg":"Correcto contraseña actualizada.","correcto":true,"co_error":"success"}';
        }else{
         $msg='{"msg":"Hubo un error, intentelo de nuevo.","correcto":false,"co_error":"danger"}';
        }
      }

    }else{
      $msg='{"msg":"Hubo un error, probablemente se intento modificar maliciosamente el formulario.","correcto":false,"co_error":"danger"}';
    }
    return $msg;
  }

  public function ingreso_formulario($data){
      // TABLAS - VALORES ENCRYP
      // DESENCRYPTAR $data
      // FUNCIONES
      //$_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      function desencriptar($cadena,$db){
          $param=str_replace("*",'+',trim($cadena));
          $str="SELECT AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') AS r;";
          $r = $db->query($str);
          $datos = $r->fetch_assoc();
          //echo $datos['r'];
          return $datos['r'];  //Devuelve el string desencriptado
      }
      function unir_valores_con_campos($campos){
        $i=0;
        while ($i < count($campos)) { // UNIR VALORES CON SU INFORMACION
            print_r($campos[$i]);
            $i++;
        };
      }
      unir_valores_con_campos($data[1]);
      //echo desencriptar('pGVdpGyuBYTKsdcmCloW22nvr5lrjIWYCuJoJ++WexMt/5TN88nDCtQ+7AbQdIJWQisFGTCKCnfvdoU7L5OxIi5lmvIxoNl3tturqFbRfgWGP01qLE3ykpFhhvqc8aoD',$_db);

      //"eyJzZWxlY3QiOmZhbHNlLCJ0aXRsZSI6IkJVU1FVRURBIERFIENMSUVOVEUgUE9SOiAiLCJvcHRpb25zIjpbWyJOb21icmUgQ2xpZW50ZSIsIm5vbWJyZV9jbGllbnRlIiwiTElLRSJdXSwiZGIiOlsiY2xpZW50ZXMiLCJpZF9jbGllbnRlcyIsW1sibm9tYnJlX2NsaWVudGUiLCJOb21icmUiXSxbInJ0biIsIlJUTiJdXV0sImRlZl90aXRsZSI6Ik5PTUJSRSJ9";
      // ####################################
      //      DATA[0] - TABLAS
      //      DATA[1] - CAMPOS
      //############## INICIALIZAR LAS TABLAS ######################
      $i=0;$str=[]; // CONSULTA ARREGLO
      $v_campos=[]; // VALOR DE LOS CAMPOS
      $n_campos=[]; // VALOR DE LOS CAMPOS
      $validaciones_error=0; // VALIDACIONES
      $validaciones_campos=''; // VALIDACIONES ARRAY
      $tablas=json_decode(desencriptar($data[0],$this->_db));
      //print_r($data);
      // ############### CASE PARA VER EL TIPO DE FORM #############
      switch ($tablas[0]) {
        case 'pa_hijos':
           $tabla_padre=$tablas[1];
           $n_campos[ $tablas[1] ]='';
           $v_campos[ $tablas[1] ]='';
          //echo "pa_hijos";
          //print_r($n_campos);
          //print_r($v_campos);

          $str_last_id="SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME  = '$tabla_padre'";
          //echo $str_last_id;
          $r = $this->_db->query($str_last_id);
          $datos = $r->fetch_assoc();
          $last_id= $datos['AUTO_INCREMENT'];

          break;

        default:
          // code...
          break;
      }
      while ($i < count($tablas)) { // CANTIDAD DE CONSULAS A REALIZAR (TABLAS) INICIALIZAR KEYS
       $n_campos[ $tablas[$i][0] ]='';
       $v_campos[ $tablas[$i][0] ]='';
       //$validaciones[ $tablas[$i][0] ]='';
       $i++;
      }
      //print_r( $n_campos);
      //print_r( $v_campos);
      $data_decode=[];$i=0;$ids_error='';
      $req=1;
      while ($i < count($data[1])) { // UNIR VALORES CON SU INFORMACION
          try{
          $data_decode[$i]= json_decode('{"valor":"'.trim($data[1][$i][1]).'",'.desencriptar($data[1][$i][0],$this->_db).'}');
          //print_r($data_decode[$i]);
          //echo $data_decode[$i]->tabla;
          $ii=0;
          while ($ii < count($data_decode[$i]->tabla)) {
            //echo $data_decode[$i]->campo[$ii];
            //echo $data_decode[$i]->valor;
            $n_campos[ $data_decode[$i]->tabla[$ii] ].=$data_decode[$i]->campo[$ii].',';
            $v_campos[ $data_decode[$i]->tabla[$ii] ].="'".$data_decode[$i]->valor."'".',';
            $ii++;
          }
          //print_r($n_campos);
          //print_r($v_campos);
          //echo($data_decode[$i]->campo);
          //$n_campos[ $data_decode[$i]->tabla ].=$data_decode[$i]->campo.',';
          //$v_campos[ $data_decode[$i]->tabla ].="'".$data_decode[$i]->valor."'".',';
          if ($data_decode[$i]->rq) {
                if (trim($data[1][$i][1])==="") {
                 $req=0;$ids_error.='"'.$data_decode[$i]->campo_form.'",';
                }
          }
          }catch(Exception $e){
           $msg_c="Error en JSON de campo: ".$i;$msg_co="danger";
           $return='{"msg_c":"'.$msg_c.'","correcto":0,"msg_co":"'.$msg_co.'","ids_error":[false]}';
           return $return;
           exit();
          }
          $i++;
      };
      $ids_error='['.trim($ids_error, ',').']';$return='';
      if ($req) {
        $i2=0;$encontro=0;$campo_error='';
        //print_r($data_decode);
        //echo "NINGUN REQUERIDO AHORA VALIDAR SI HAY PARAMETROS DE VALIDACION";
        while ($i2 < count($data[1])) { // UNIR VALORES CON SU INFORMACION
          //print_r($data_decode);
          $campo_error=$data_decode[$i2]->campo_form;
          //echo "| CAMPO ".$i2.' es '.$data_decode[$i2]->campo.' |';
          if ($data_decode[$i2]->validaciones[0]) {
            $j=0;
            //echo count($data_decode[$i2]->validaciones[1]);
            while ($j < count($data_decode[$i2]->validaciones[1])) {
              //echo "* ".count($data_decode[$i2]->validaciones[1])." Validaciones *";
              //echo "( ".$data_decode[$i2]->validaciones[1][$j][0]." )";

              switch ($data_decode[$i2]->validaciones[1][$j][0]) {
                case 'db':
                  $key_campos_select=$data_decode[$i2]->validaciones[1][$j][3];
                  $param_select=str_replace("?", $data_decode[$i2]->$key_campos_select, $data_decode[$i2]->validaciones[1][$j][2].' AS r');
                  // #### #### #### #### #### #### #### ####
                  $key_where=$data_decode[$i2]->validaciones[1][$j][5];
                  $param_where=str_replace("?", "'".$data_decode[$i2]->$key_where, $data_decode[$i2]->validaciones[1][$j][4]."'");
                  // UNIR PARAM SELECT CON QUERY
                  $query=str_replace("?", $param_select, $data_decode[$i2]->validaciones[1][$j][1]);
                  // CONSULTAR EN LA DB
                  $r = $_db->query($query.$param_where);
                  $datos = $r->fetch_assoc();
                  //echo $datos['r'];
                  if (!$datos['r']) {
                    // GENERAR EL MENSAJE DE ERROR
                    $param_msg=$data_decode[$i2]->validaciones[1][$j][6];
                    $msg=str_replace("?", $param_msg, $data_decode[$i2]->validaciones[1][$j][7]);
                    $validaciones_campos.='["'.$msg.'","danger"],';
                    $encontro=1;
                    //echo "FALSO";
                  }
                  //return $datos['r'];
                  //echo $param_select;
                  //echo $param_where;
                  //echo $query.$param_where;
                  //print_r($data_decode[$i2]->validaciones[1][$j]);
                 break;
                case 'number':
                  if (!(is_numeric($data_decode[$i2]->valor) && $data_decode[$i2]->valor>0)) {
                    $encontro=1;
                    $key=$data_decode[$i2]->validaciones[1][$j][1];
                    $msg= str_replace("?", $data_decode[$i2]->$key, $data_decode[$i2]->validaciones[1][$j][2]);
                    $validaciones_campos.='["'.$msg.'","danger"],';
                  }
                 break;
                case 'decimal':
                //echo "VALIDAR DECIMAL CON VALOR ".$data_decode[$i2]->valor;
                  //echo $data_decode[$i2]->valor;
                  if (!is_numeric($data_decode[$i2]->valor)) {
                    $key=$data_decode[$i2]->validaciones[1][$j][1];
                    $msg= str_replace("?", $data_decode[$i2]->$key, $data_decode[$i2]->validaciones[1][$j][2]);
                    //$validaciones_error=1;
                    //$validaciones_campos.='["'.$msg.'","danger"],';
                    $validaciones_campos.='["'.$msg.'","danger"],';
                    $encontro=1;
                  //echo "NO ES";
                   /*$float=floatval($data_decode[$i2]->valor);
                    //echo $float;
                    //var_dump($float);
                              //echo ' -- '.$float;
                    if (!$float) {
                      //echo "<NO FLOAT> JJJ ".$j;
                      $key=$data_decode[$i2]->validaciones[1][$j][1];
                      $msg= str_replace("?", $data_decode[$i2]->$key, $data_decode[$i2]->validaciones[1][$j][2]);
                      //$validaciones_error=1;
                      //$validaciones_campos.='["'.$msg.'","danger"],';
                      $validaciones_campos.='["'.$msg.'","danger"],';
                      $encontro=1;
                   }*/
                  }
                 break;
                case 'if':
                  $key_if=$data_decode[$i2]->validaciones[1][$j][1];
                  $key_msg=$data_decode[$i2]->validaciones[1][$j][3];
                  $if_eval=$data_decode[$i2]->validaciones[1][$j][2];
                  //print_r($data_decode[$i2]->validaciones[1][$j]);
                  $valor_msg=$data_decode[$i2]->$key_msg;
                  $valor_if=$data_decode[$i2]->$key_if;
                  //echo $if_eval;
                  $msg= str_replace("?", $valor_msg, $data_decode[$i2]->validaciones[1][$j][4]);
                  $if_eval= str_replace("?", '"'.$data_decode[$i2]->valor.'"',$data_decode[$i2]->validaciones[1][$j][2]);
                  //$validaciones_error=1;
                  //$if_eval="if(".$if_eval."){echo 1};else{echo 0;}";
                  eval('if('.$if_eval.'){$res=1;}else{$res=0;}');
                  if (!$res) {
                    $validaciones_campos.='["'.$msg.'","danger"],';
                    $encontro=1;
                  }
                break;
              }
              //echo "SALIO CON  J ES ".($j).' i2 ES '.$i2;
              $j++;
            }

          }
          if ($encontro) {
             break;
          }
          $i2++;
          //$i2=100;
        }
        if ($encontro) {
          $validaciones_campos='['.trim($validaciones_campos, ',').']';
          //$ids_error.='"'.$data_decode[$i]->campo_form.'",';
          $return='{"error_validacion":"1","correcto":0,"ids_val":[true,["'.$campo_error.'"]],"campos":'.$validaciones_campos.'}';
        }else{
          $j2=0;
          $_db->autocommit(false);
          $insert_true=true;
          while ($j2 < count($tablas)) {
            //$n_campos[ $tablas[$j2] ] = '('.trim($n_campos[ $tablas[$j2] ], ',').')';
            $campos='$n_campos';
            $valores='$v_campos';
            eval($campos."['".$tablas[$j2][0]."']='('.trim(".$campos."['".$tablas[$j2][0]."'],',').')';");
            eval($valores."['".$tablas[$j2][0]."']='('.trim(".$valores."['".$tablas[$j2][0]."'],',').')';");
            //eval('$str="'.$valores."['".$tablas[$j2][0]."'],',').')';");
            //echo '$str="INSERT INTO".'.$campos."['".$tablas[$j2][0]."'].".'" VALUES ".'.$valores."['".$tablas[$j2][0]."'].".'";"';
            eval('$str="INSERT INTO '.$tablas[$j2][0].'".'.$campos."['".$tablas[$j2][0]."'].".'" VALUES ".'.$valores."['".$tablas[$j2][0]."'].".'";";');
            //echo('$str="INSERT INTO '.$tablas[$j2][0].'".'.$campos."['".$tablas[$j2][0]."'].".'" VALUES ".'.$valores."['".$tablas[$j2][0]."'].".'";";');
            //echo $str;
            $insert=$_db->query($str);
            //echo $str;
            if (!$insert) {
              $insert_true=false;
            }
            //eval($a."['".$tablas[$j2][0]."']='('.trim(".$a."['".$tablas[$j2][0]."'],',').')';");
            //$str="INSERT INTO ".$tablas[$j2][0]." VALUES ".$v_campos->$tablas[$j2];
            //echo $str;
            //echo $aa;
            //echo($tablas[$j2][0]);
            $j2++;
          }
          if ($insert_true) {
              $_db->commit();
              $_db->autocommit(true);
              //$retorno['msg_t'] ="EXITO";
              $msg_c="Formulario ingresado con éxito.";
              $msg_co ="success"; // COLOR ALERTA
              $return='{"msg_c":"'.$msg_c.'","correcto":1,"msg_co":"'.$msg_co.'"}';

          }else{
              //$insert_true=false;
              //$retorno['querys'] =$querys;
              //$msg_c="Error al procesar el formulario, verifique el campo: <br>".$errores;
              //$msg_c="Error al procesar el formulario, verifique el campo: <br>".$errores.' '.$errno;
              $msg_c="Error al procesar el formulario";
              $msg_co="danger";
              $_db->rollback();
              $_db->autocommit(true);
              //$return='{"msg_c":"'.$msg_c.'","correcto":false,"msg_co":"'.$msg_co.'","ids_error":[true,["'.$id_html_error.'"]]}';
              $return='{"msg_c":"'.$msg_c.'","correcto":false,"msg_co":"'.$msg_co.'"}';
          }
          //$aa="trim(".$n_campos."['".$tablas[$j2][0]."'],',')";
          //echo $aa;
          //echo '('.trim($n_campos['garantias'],',').')';
          //print_r($n_campos);
          //print_r($v_campos);
          //$str[ $tablas[$i2] ].=$n_campos[ $tablas[$i2] ].' VALUES '.$v_campos[ $tablas[$i2] ];
          //$n_campos[ $tablas[$i2] ] = '('.trim($n_campos[ $tablas[$i2] ], ',').')';
          //$v_campos[ $tablas[$i2] ] = '('.trim($v_campos[ $tablas[$i2] ], ',').')';
          //$str[ $tablas[$i2] ].=$n_campos[ $tablas[$i2] ].' VALUES '.$v_campos[ $tablas[$i2] ];
          //$insert=$this->_db->query($str[ $tablas[$i2] ]);
        }
      }else{
          $msg_c="Falta completar datos del Formulario";$msg_co="danger";
          $return='{"msg_c":"'.$msg_c.'","correcto":'.$req.',"msg_co":"'.$msg_co.'","ids_error":[true,'.$ids_error.']}';
      }
      return $return;
  }

  public function get_info_user($us){
      $str="SELECT CONCAT('[[\"',nombre_usuario,'\",',id_usuario,'],\"',centro_regional,'\"]') AS r FROM man_usuarios WHERE nombre_usuario='$us'";
      //echo $str;
      $r = $this->_db->query($str); // HACER LA CONSULTA
      $datos = $r->fetch_assoc();
      return $datos['r']; // RESPUESTA
  }
   public function get_us_valido($us,$p){
      $str="SELECT REPLACE(fn_valida_usuario('$us','$p'),'\n','') AS r";
      $str2="SELECT centro_regional FROM man_usuarios WHERE nombre_usuario='$us''"; // PARA TRAER EL NUMERO DE SECCIONAL
      //echo $str2;
      $r = $this->_db->query($str); // HACER LA CONSULTA
      $datos = $r->fetch_assoc();
      return $datos['r']; // RESPUESTA
      /*$stmt = $this->_db->prepare("SELECT fn_valida_usuario(?, ?) AS r;");
      $stmt->bind_param('ss', $us, $p);
      $stmt->execute();
      $r = $stmt->get_result();
      //$resultado = $sentencia->get_result();
      $fila = $r->fetch_assoc();
      return $fila['r'];*/
  }
  public function set_reset_pass($us,$cod){
      $str="SELECT fn_reset_us('$us', '$cod') AS r;";
      $r = $this->_db->query($str); // HACER LA CONSULTA
      $datos = $r->fetch_assoc();
      return $datos['r'];
  }
  public function get_row($data){
    function desencriptar($cadena){
           $__K_K='__K&K';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
           $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($__K_K), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($__K_K))), "\0");
          return $decrypted;  //Devuelve el string desencriptado
    }
    //print_r($data);
    $str=json_decode(desencriptar($data[1]));
    if (!json_last_error()) {
      $q=str_replace("?", $data[0], $str[0]);
      $r= $this->_db->query($q);
      $fila=$r->fetch_array(MYSQLI_NUM);
      return '{"correcto":true,"valor":"'.$fila[0].'"}';
    }else{

    }
    //echo json_last_error();
    //print_r($str);
  }
  public function get_b_dinamica($query_encode,$query){
    function desencriptar($cadena){
           $__K_K='__K&K';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
           $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($__K_K), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($__K_K))), "\0");
          return $decrypted;  //Devuelve el string desencriptado
    }
    $str=json_decode(desencriptar($query_encode));
    if (!json_last_error()) {
      $q=str_replace("?", $query, $str[0]);
      $r= $this->_db->query($q);
      //$fila=$r->fetch_array(MYSQLI_NUM);
      $cadena_json='';
      while ($fila = $r->fetch_array(MYSQLI_NUM)) {
        $cadena_json.='{"value":"'.$fila[1].'","v":"'.$fila[0].'"},';
      }
      //$cadena_json.=trim($cadena_json, ',').']}';
      return '{"suggestions": ['.trim($cadena_json, ',').']}';
    }else{

    }

  }
  public function get_tabla($data){
      // - ROWS O TOTAL $data[0] AHORA YA NO IRA
      // - CAMPOS $data[1][i]
      // - VISTA $data[2]
      // - PAGINACION (inicio - fin)
      //     $data[3][0]  comienzo
      //     $data[3][1]  limite de registros
      // - WHERES CON FILTROS O SIN FILTROS $data[4]
      // - WHERES ESPECIALES $data[6]
      // - ORDER BY $data[7]
      echo $data[7];
      $i=0;$i2=1;
      switch ($data[0]) {
          case 'rows':
              $pag='';
              if ($data[3]) {
                $pag=' LIMIT '.$data[3][0].','.$data[3][1];
              }
              //$pag=$data[3][0].','.$data[3][1];
              //$t=$data[3][1]-$data[3][0];
              $campos='';
              while ($i < count($data[1])) {
                  $campos.=$data[1][$i].',';
                  $i++;
              }
              $campos=trim($campos, ',');
              $where='';
              $selects_return='';
              if (@$data[4]) {
                 $i=0;
                 while ($i < count($data[4])) { // FILTROS HIJOS
                  $arr_select='';
                  $data_row=$data[4][$i];
                  $fk=$data_row[0];
                  $nombre=$data_row[1];
                  $def_name=$data_row[2][0];
                  $def_val=$data_row[2][1];
                  $n_filtro='"'.$data_row[3].'",';
                  $hay_set=0;
                  $opt_select=0;
                  //echo $def_name;
                  if (gettype($def_name)=='string') {
                      $wk='';
                      if ( $data[6] ) {
                          $wk.="AND ".$data[6];
                      }
                      $str1="SELECT '$def_val' AS 'A', '$def_name' AS 'B'";
                      $str2="SELECT DISTINCT($fk) AS A, $nombre AS B FROM ".$data[2]." WHERE 1 ".$wk." ORDER BY ".$fk;
                      //echo $str;
                      $r1= $this->_db->query($str1);
                      $r2= $this->_db->query($str2);
                      while ($fila1 = $r1->fetch_assoc()) {
                        $arr_select.='["'.$fila1['A'].'","'.$fila1['B'].'"],';
                      }
                      while ($fila2 = $r2->fetch_assoc()) {
                        $arr_select.='["'.$fila2['A'].'","'.$fila2['B'].'"],';
                      }
                  }
                  if (gettype($def_name)=='boolean') {
                      $hay_set=1;
                      $str="SELECT DISTINCT($fk) AS 'A', $nombre AS 'B' FROM ".$data[2]." ORDER BY ".$fk;
                      // EXISTE EL PARAMETRO DENTRO DE LOS RESULTADOS ESPECIDICADOS
                      // DE NO EXISTIR ESTE PARAMETRO ES QUE NO SE DEBE MOSTRAR EN PANTALLA
                      $r= $this->_db->query($str);
                      //print_r($this->_db);
                      //echo $str;
                      $def_valpao=$def_val;$eciste_param=false;
                      while ($fila = $r->fetch_assoc()) {
                        //echo ($def_val==$fila['A']);
                        //echo $def_val.'/'.$fila['A'].'('.($def_val==$fila['A']).')';
                        if ($def_val==$fila['A']) {
                          if (gettype($def_val)=="string") {
                              $def_val='"'.$def_val.'"';
                          }
                          $opt_select=$def_val; $eciste_param=true;
                        }
                        $arr_select.='["'.$fila['A'].'","'.$fila['B'].'"],';
                      }
                      //echo "=> ".$arr_select;
                      if ($eciste_param) {
                        $where.="AND ".$fk."='".$def_valpao."' ";
                      }

                  }

                  //$campos.=$data[1][$i].',';
                  $arr_select = '["'.$fk.'",'.$n_filtro.$hay_set.','.$opt_select.','.'['.trim($arr_select, ',').']]';
                  $selects_return.=$arr_select.',';
                  $i++;
                 }
                  $selects_return = '['.trim($selects_return, ',').']';

                  //echo $selects_return;


                 //$where='AND '.$data[4];
              }

              if ($data[5] ) {
                $i2=0;$a='';
                while ($i2 < count($data[5])) {
                  if ($data[5][$i2][0]) {
                    $where.="AND ".$data[5][$i2][0]."='".$data[5][$i2][1]."' ";
                  }
                  $i2++;
                }
              }
              if ($data[6] ) {
                  $where.="AND ".$data[6];
              }
              $order='';
              $str="SELECT $campos FROM ".$data[2]." WHERE 1 ".$where."$pag";
              if ( $data[7] ) {
                  $order.=$data[7];
                  $str="SELECT $campos FROM ".$data[2]." WHERE 1 ".$where." ORDER BY ".$order.$pag;
              }
              //echo $a;

              //echo $str;
              $r = $this->_db->query($str); // HACER LA CONSULTA
              $str2="SELECT COUNT(*) AS t FROM ".$data[2]." WHERE 1 ".$where;
              //echo  $str;
              $r2= $this->_db->query($str2);
              $fila=$r2->fetch_assoc();
              $t_db=$fila['t']; // TOTAL DB
              $t=$r->num_rows;
              $a=json_encode($data);
              $cadena_json='{"correcto":true,"total":'.$t.',"a":'.$a.',"str":"'.$str.'","total_db":'.$t_db.',"registros":[';
              $i=0;
              while ($fila = $r->fetch_assoc()) {  // TOTAL FILAS
                  $row='[';
                   while ($i < count($data[1])) {
                      $row.='"'.$fila[ $data[1][$i] ].'",' ;
                      $i++;
                   }
                   $row = trim($row, ',').']';
                  $i=0;
                  $cadena_json.=$row.',';
                  $i2++;
              }
              ;
              //$str = trim($str, ',');$str.=" FROM ".$data[2];
              if ($data[4]) {$cadena_json = trim($cadena_json, ',').'],"select":'.$selects_return.'}';}else{$cadena_json = trim($cadena_json, ',').']}';}
              return $cadena_json;
              break;

          case 'total':
              $str="SELECT COUNT(*) AS conteo FROM ".$data[1];
              $r = $this->_db->query($str);
              $fila=$r->fetch_assoc();
              $cadena_json='{"total":'.$fila['conteo'].'}';
              return $cadena_json;
              break;

              default:
              return '["error"]';
              break;
      }
      //return $data[0];
      /*$stmt = $this->_db->prepare("SELECT fn_valida_usuario(?, ?) AS r;");
      $stmt->bind_param('ss', $us, $p);
      $stmt->execute();
      $r = $stmt->get_result();
      //$resultado = $sentencia->get_result();
      $fila = $r->fetch_assoc();
      return $fila['r'];*/
  }

}
?>



COPIA INGRESO FORM
while ($i < count($tablas)) { // CANTIDAD DE CONSULAS A REALIZAR (TABLAS) INICIALIZAR KEYS
 $n_campos[ $tablas[$i][0] ]='';
 $v_campos[ $tablas[$i][0] ]='';
 //$validaciones[ $tablas[$i][0] ]='';
 $i++;
}
//print_r( $n_campos);
//print_r( $v_campos);
$data_decode=[];$i=0;$ids_error='';
$req=1;
while ($i < count($data[1])) { // UNIR VALORES CON SU INFORMACION
    try{
    $data_decode[$i]= json_decode('{"valor":"'.trim($data[1][$i][1]).'",'.desencriptar($data[1][$i][0],$this->_db).'}');
    //print_r($data_decode[$i]);
    //echo $data_decode[$i]->tabla;
    $ii=0;
    while ($ii < count($data_decode[$i]->tabla)) {
      //echo $data_decode[$i]->campo[$ii];
      //echo $data_decode[$i]->valor;
      $n_campos[ $data_decode[$i]->tabla[$ii] ].=$data_decode[$i]->campo[$ii].',';
      $v_campos[ $data_decode[$i]->tabla[$ii] ].="'".$data_decode[$i]->valor."'".',';
      $ii++;
    }
    //print_r($n_campos);
    //print_r($v_campos);
    //echo($data_decode[$i]->campo);
    //$n_campos[ $data_decode[$i]->tabla ].=$data_decode[$i]->campo.',';
    //$v_campos[ $data_decode[$i]->tabla ].="'".$data_decode[$i]->valor."'".',';
    if ($data_decode[$i]->rq) {
          if (trim($data[1][$i][1])==="") {
           $req=0;$ids_error.='"'.$data_decode[$i]->campo_form.'",';
          }
    }
    }catch(Exception $e){
     $msg_c="Error en JSON de campo: ".$i;$msg_co="danger";
     $return='{"msg_c":"'.$msg_c.'","correcto":0,"msg_co":"'.$msg_co.'","ids_error":[false]}';
     return $return;
     exit();
    }
    $i++;
};
$ids_error='['.trim($ids_error, ',').']';$return='';
if ($req) {
  $i2=0;$encontro=0;$campo_error='';
  //print_r($data_decode);
  //echo "NINGUN REQUERIDO AHORA VALIDAR SI HAY PARAMETROS DE VALIDACION";
  while ($i2 < count($data[1])) { // UNIR VALORES CON SU INFORMACION
    //print_r($data_decode);
    $campo_error=$data_decode[$i2]->campo_form;
    //echo "| CAMPO ".$i2.' es '.$data_decode[$i2]->campo.' |';
    if ($data_decode[$i2]->validaciones[0]) {
      $j=0;
      //echo count($data_decode[$i2]->validaciones[1]);
      while ($j < count($data_decode[$i2]->validaciones[1])) {
        //echo "* ".count($data_decode[$i2]->validaciones[1])." Validaciones *";
        //echo "( ".$data_decode[$i2]->validaciones[1][$j][0]." )";

        switch ($data_decode[$i2]->validaciones[1][$j][0]) {
          case 'db':
            $key_campos_select=$data_decode[$i2]->validaciones[1][$j][3];
            $param_select=str_replace("?", $data_decode[$i2]->$key_campos_select, $data_decode[$i2]->validaciones[1][$j][2].' AS r');
            // #### #### #### #### #### #### #### ####
            $key_where=$data_decode[$i2]->validaciones[1][$j][5];
            $param_where=str_replace("?", "'".$data_decode[$i2]->$key_where, $data_decode[$i2]->validaciones[1][$j][4]."'");
            // UNIR PARAM SELECT CON QUERY
            $query=str_replace("?", $param_select, $data_decode[$i2]->validaciones[1][$j][1]);
            // CONSULTAR EN LA DB
            $r = $_db->query($query.$param_where);
            $datos = $r->fetch_assoc();
            //echo $datos['r'];
            if (!$datos['r']) {
              // GENERAR EL MENSAJE DE ERROR
              $param_msg=$data_decode[$i2]->validaciones[1][$j][6];
              $msg=str_replace("?", $param_msg, $data_decode[$i2]->validaciones[1][$j][7]);
              $validaciones_campos.='["'.$msg.'","danger"],';
              $encontro=1;
              //echo "FALSO";
            }
            //return $datos['r'];
            //echo $param_select;
            //echo $param_where;
            //echo $query.$param_where;
            //print_r($data_decode[$i2]->validaciones[1][$j]);
           break;
          case 'number':
            if (!(is_numeric($data_decode[$i2]->valor) && $data_decode[$i2]->valor>0)) {
              $encontro=1;
              $key=$data_decode[$i2]->validaciones[1][$j][1];
              $msg= str_replace("?", $data_decode[$i2]->$key, $data_decode[$i2]->validaciones[1][$j][2]);
              $validaciones_campos.='["'.$msg.'","danger"],';
            }
           break;
          case 'decimal':
          //echo "VALIDAR DECIMAL CON VALOR ".$data_decode[$i2]->valor;
            //echo $data_decode[$i2]->valor;
            if (!is_numeric($data_decode[$i2]->valor)) {
              $key=$data_decode[$i2]->validaciones[1][$j][1];
              $msg= str_replace("?", $data_decode[$i2]->$key, $data_decode[$i2]->validaciones[1][$j][2]);
              //$validaciones_error=1;
              //$validaciones_campos.='["'.$msg.'","danger"],';
              $validaciones_campos.='["'.$msg.'","danger"],';
              $encontro=1;
            //echo "NO ES";
             /*$float=floatval($data_decode[$i2]->valor);
              //echo $float;
              //var_dump($float);
                        //echo ' -- '.$float;
              if (!$float) {
                //echo "<NO FLOAT> JJJ ".$j;
                $key=$data_decode[$i2]->validaciones[1][$j][1];
                $msg= str_replace("?", $data_decode[$i2]->$key, $data_decode[$i2]->validaciones[1][$j][2]);
                //$validaciones_error=1;
                //$validaciones_campos.='["'.$msg.'","danger"],';
                $validaciones_campos.='["'.$msg.'","danger"],';
                $encontro=1;
             }*/
            }
           break;
          case 'if':
            $key_if=$data_decode[$i2]->validaciones[1][$j][1];
            $key_msg=$data_decode[$i2]->validaciones[1][$j][3];
            $if_eval=$data_decode[$i2]->validaciones[1][$j][2];
            //print_r($data_decode[$i2]->validaciones[1][$j]);
            $valor_msg=$data_decode[$i2]->$key_msg;
            $valor_if=$data_decode[$i2]->$key_if;
            //echo $if_eval;
            $msg= str_replace("?", $valor_msg, $data_decode[$i2]->validaciones[1][$j][4]);
            $if_eval= str_replace("?", '"'.$data_decode[$i2]->valor.'"',$data_decode[$i2]->validaciones[1][$j][2]);
            //$validaciones_error=1;
            //$if_eval="if(".$if_eval."){echo 1};else{echo 0;}";
            eval('if('.$if_eval.'){$res=1;}else{$res=0;}');
            if (!$res) {
              $validaciones_campos.='["'.$msg.'","danger"],';
              $encontro=1;
            }
          break;
        }
        //echo "SALIO CON  J ES ".($j).' i2 ES '.$i2;
        $j++;
      }

    }
    if ($encontro) {
       break;
    }
    $i2++;
    //$i2=100;
  }
  if ($encontro) {
    $validaciones_campos='['.trim($validaciones_campos, ',').']';
    //$ids_error.='"'.$data_decode[$i]->campo_form.'",';
    $return='{"error_validacion":"1","correcto":0,"ids_val":[true,["'.$campo_error.'"]],"campos":'.$validaciones_campos.'}';
  }else{
    $j2=0;
    $_db->autocommit(false);
    $insert_true=true;
    while ($j2 < count($tablas)) {
      //$n_campos[ $tablas[$j2] ] = '('.trim($n_campos[ $tablas[$j2] ], ',').')';
      $campos='$n_campos';
      $valores='$v_campos';
      eval($campos."['".$tablas[$j2][0]."']='('.trim(".$campos."['".$tablas[$j2][0]."'],',').')';");
      eval($valores."['".$tablas[$j2][0]."']='('.trim(".$valores."['".$tablas[$j2][0]."'],',').')';");
      //eval('$str="'.$valores."['".$tablas[$j2][0]."'],',').')';");
      //echo '$str="INSERT INTO".'.$campos."['".$tablas[$j2][0]."'].".'" VALUES ".'.$valores."['".$tablas[$j2][0]."'].".'";"';
      eval('$str="INSERT INTO '.$tablas[$j2][0].'".'.$campos."['".$tablas[$j2][0]."'].".'" VALUES ".'.$valores."['".$tablas[$j2][0]."'].".'";";');
      //echo('$str="INSERT INTO '.$tablas[$j2][0].'".'.$campos."['".$tablas[$j2][0]."'].".'" VALUES ".'.$valores."['".$tablas[$j2][0]."'].".'";";');
      //echo $str;
      $insert=$_db->query($str);
      //echo $str;
      if (!$insert) {
        $insert_true=false;
      }
      //eval($a."['".$tablas[$j2][0]."']='('.trim(".$a."['".$tablas[$j2][0]."'],',').')';");
      //$str="INSERT INTO ".$tablas[$j2][0]." VALUES ".$v_campos->$tablas[$j2];
      //echo $str;
      //echo $aa;
      //echo($tablas[$j2][0]);
      $j2++;
    }
    if ($insert_true) {
        $_db->commit();
        $_db->autocommit(true);
        //$retorno['msg_t'] ="EXITO";
        $msg_c="Formulario ingresado con éxito.";
        $msg_co ="success"; // COLOR ALERTA
        $return='{"msg_c":"'.$msg_c.'","correcto":1,"msg_co":"'.$msg_co.'"}';

    }else{
        //$insert_true=false;
        //$retorno['querys'] =$querys;
        //$msg_c="Error al procesar el formulario, verifique el campo: <br>".$errores;
        //$msg_c="Error al procesar el formulario, verifique el campo: <br>".$errores.' '.$errno;
        $msg_c="Error al procesar el formulario";
        $msg_co="danger";
        $_db->rollback();
        $_db->autocommit(true);
        //$return='{"msg_c":"'.$msg_c.'","correcto":false,"msg_co":"'.$msg_co.'","ids_error":[true,["'.$id_html_error.'"]]}';
        $return='{"msg_c":"'.$msg_c.'","correcto":false,"msg_co":"'.$msg_co.'"}';
    }
    //$aa="trim(".$n_campos."['".$tablas[$j2][0]."'],',')";
    //echo $aa;
    //echo '('.trim($n_campos['garantias'],',').')';
    //print_r($n_campos);
    //print_r($v_campos);
    //$str[ $tablas[$i2] ].=$n_campos[ $tablas[$i2] ].' VALUES '.$v_campos[ $tablas[$i2] ];
    //$n_campos[ $tablas[$i2] ] = '('.trim($n_campos[ $tablas[$i2] ], ',').')';
    //$v_campos[ $tablas[$i2] ] = '('.trim($v_campos[ $tablas[$i2] ], ',').')';
    //$str[ $tablas[$i2] ].=$n_campos[ $tablas[$i2] ].' VALUES '.$v_campos[ $tablas[$i2] ];
    //$insert=$this->_db->query($str[ $tablas[$i2] ]);
  }
}else{
    $msg_c="Falta completar datos del Formulario";$msg_co="danger";
    $return='{"msg_c":"'.$msg_c.'","correcto":'.$req.',"msg_co":"'.$msg_co.'","ids_error":[true,'.$ids_error.']}';
}
