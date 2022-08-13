  <?php
  //header('Content-Type: application/json');
  require_once "config_db.php";
  class DbModelo
  {
    public $_db;
    public function __construct()
    {
      $this->_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
      if ($this->_db->connect_errno) {
        echo "Fallo al conectar a MySQL: " . $this->_db->connect_error;
        return;
      }
      $this->_db->set_charset("utf8");
    }
    public function set_guardar_imagen($emp, $user)
    {
      $str = "INSERT INTO fotos_subidas(n_emp,usuario) VALUES('$emp','$user')";
      $r = $this->_db->query($str);
      return $r;
    }
    public function desencriptar($cadena)
    {
      $param = str_replace("*", '+', trim($cadena));
      $str = "SELECT AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') AS r;";
      $r = $this->_db->query($str);
      $datos = $r->fetch_assoc();
      //echo $cadena;
      //echo $datos['r'];
      return $datos['r'];
    }
    public function reset_pass($data)
    {
      function desencriptar($cadena)
      {
        $__K_K = '__K&K';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($__K_K), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($__K_K))), "\0");
        return $decrypted;  //Devuelve el string desencriptado
      }
      $user = json_decode(desencriptar($data[0]));
      if ($user[0] != "" && ($data[1] === $data[2])) {
        if ($data[1] === $data[3]) {
          $msg = '{"msg":"Debe establecer una contaseña diferente a la que fue brindada.","correcto":false,"co_error":"danger"}';
        } else {
          $user = $user[0];
          $pass = $data[1];
          //echo $pass_old;
          $str = "SELECT reset_pass_us('$pass', '$user') AS r;";
          $r = $this->_db->query($str); // HACER LA CONSULTA
          $datos = $r->fetch_assoc();
          if ($datos['r']) {
            $msg = '{"msg":"Correcto contraseña actualizada.","correcto":true,"co_error":"success"}';
          } else {
            $msg = '{"msg":"Hubo un error, intentelo de nuevo.","correcto":false,"co_error":"danger"}';
          }
        }
      } else {
        $msg = '{"msg":"Hubo un error, probablemente se intento modificar maliciosamente el formulario.","correcto":false,"co_error":"danger"}';
      }
      return $msg;
    }

    public function ingreso_formulario($data)
    {
      // TABLAS - VALORES ENCRYP
      // DESENCRYPTAR $data
      // FUNCIONES
      //print_r($data);
      function arr_campos($campos, $db)
      {
        $ids_error = '';
        $data_decode = [];
        $input_multi = [];
        $n_campos = ''; //;
        $v_campos = ''; //;
        $i = 0;
        $input_multi_key = [];
        while ($i < count($campos)) { // UNIR VALORES CON SU INFORMACION
          $data_decode[$i] = json_decode('{"valor":"' . trim($campos[$i][1]) . '",' . desencriptar($campos[$i][0], $db) . '}');

          //echo $data_decode[$i]->tipo;
          if ($data_decode[$i]->rq) {
            if (trim($campos[$i][1]) === "") {
              $ids_error .= '"' . $data_decode[$i]->campo_form . '",';
            }
          }
          if ($data_decode[$i]->tipo == 'multi') {
            //echo $data_decode[$i]->arr;
            $campos_multi_key[$data_decode[$i]->arr] .= $data_decode[$i]->campo . ',';
            $values_multi_key[$data_decode[$i]->arr] .= "'" . $data_decode[$i]->valor . "',";
            //array_push($input_multi[0], $data_decode[$i]);
            //array_push($input_multi[1], $data_decode[$i]);
          }
          if ($data_decode[$i]->tipo == 'normal') {
            $n_campos .= $data_decode[$i]->campo . ','; // O CAMPO [0]
            $v_campos .= "'" . $data_decode[$i]->valor . "'" . ',';
            //echo $data_decode[$i]->valor;
            //$n_campos[ $data_decode[$i]->tabla ].=$data_decode[$i]->campo.',';
            //$v_campos[ $data_decode[$i]->tabla ].="'".$data_decode[$i]->valor."'".',';
          }

          $i++;
        }
        //print_r($n_campos);
        //print_r($v_campos);
        $n_campos = trim($n_campos, ',');
        $v_campos = trim($v_campos, ',');
        $ids_error = '[' . trim($ids_error, ',') . ']';
        //print_r($campos_multi_key);
        //print_r($values_multi_key);
        return [$data_decode, $ids_error, [$campos_multi_key, $values_multi_key], [$n_campos, $v_campos]];
      }
      //$_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      function desencriptar($cadena, $db)
      {
        $param = str_replace("*", '+', trim($cadena));
        $str = "SELECT AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') AS r;";
        $r = $db->query($str);
        $datos = $r->fetch_assoc();
        //echo $datos['r'];
        return $datos['r'];  //Devuelve el string desencriptado
      }
      //print_r(arr_campos($data[1],$this->_db));
      //echo desencriptar('pGVdpGyuBYTKsdcmCloW22nvr5lrjIWYCuJoJ++WexMt/5TN88nDCtQ+7AbQdIJWQisFGTCKCnfvdoU7L5OxIi5lmvIxoNl3tturqFbRfgWGP01qLE3ykpFhhvqc8aoD',$_db);

      //"eyJzZWxlY3QiOmZhbHNlLCJ0aXRsZSI6IkJVU1FVRURBIERFIENMSUVOVEUgUE9SOiAiLCJvcHRpb25zIjpbWyJOb21icmUgQ2xpZW50ZSIsIm5vbWJyZV9jbGllbnRlIiwiTElLRSJdXSwiZGIiOlsiY2xpZW50ZXMiLCJpZF9jbGllbnRlcyIsW1sibm9tYnJlX2NsaWVudGUiLCJOb21icmUiXSxbInJ0biIsIlJUTiJdXV0sImRlZl90aXRsZSI6Ik5PTUJSRSJ9";
      // ####################################
      //      DATA[0] - TABLAS
      //      DATA[1] - CAMPOS
      //############## INICIALIZAR LAS TABLAS ######################
      $i = 0;
      $str = []; // CONSULTA ARREGLO
      $v_campos = []; // VALOR DE LOS CAMPOS
      $n_campos = []; // VALOR DE LOS CAMPOS
      $validaciones_error = 0; // VALIDACIONES
      $validaciones_campos = ''; // VALIDACIONES ARRAY
      $tablas = json_decode(desencriptar($data[0], $this->_db));

      // ############### CASE PARA VER EL TIPO DE FORM #############
      // TODOS ESTAN LLENOS
      $campos = (arr_campos($data[1], $this->_db));
      //print_r($campos);
      //echo strlen($campos[1]);
      if (strlen($campos[1]) > 2) {
        //echo "HAY VACIOS";
        $msg_c = "Falta completar datos del Formulario";
        $msg_co = "danger";
        $return = '{"msg_c":"' . $msg_c . '","correcto":false,"msg_co":"' . $msg_co . '","ids_error":[true,' . $campos[1] . ']}';
      } else {
        //print_r( $tablas[0]);
        switch ($tablas[0]) {
          case 'pa_hijos':
            $tabla_padre = $tablas[1];
            //echo "pa_hijos";
            //print_r($campos);
            //print_r($v_campos);

            $str_last_id = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME  = '$tabla_padre'";
            //echo $str_last_id;
            $r = $this->_db->query($str_last_id);
            $datos = $r->fetch_assoc();
            $last_id = $datos['AUTO_INCREMENT'];
            $i_saltos = 0;
            $multi_insert = [];
            $tabla_multi = $tablas[3];
            $where_multi = $tablas[4];
            $n_insert = $tablas[5]; // NUMERO DE INSERT
            $campos_row = $tablas[6]; // CAMPOS POR INSERT
            $k = 0;
            //echo "soy campos row ".$campos_row;
            $multi_str = '';
            $tabla = $tablas[3];
            while ($k < $n_insert) { // CANTIDAD DE CONSULAS A REALIZAR (TABLAS) INICIALIZAR KEYS
              //echo $tablas[4];
              //$k2=0;
              $multi_str .= "INSERT INTO " . $tabla . "(" . $tablas[4] . ',' . trim($campos[2][0][$k], ',') . ") VALUES ('" . $last_id . "'," . trim($campos[2][1][$k], ',') . ");";
              //echo $campos[2][0][$k];
              //echo $campos[2][1][$k];
              $k++;
            }
            //$this->_db->autocommit(false);
            //echo $multi_str;
            $str = "INSERT INTO " . $tablas[1] . " (" . $campos[3][0] . ")" . " VALUES(" . $campos[3][1] . ")";
            //echo $str;            //
            //print_r($campos[3]);
            $insert_padre = $this->_db->query($str);
            $insert_hijos = $this->_db->multi_query($multi_str);
            //echo $insert_padre;
            //echo $insert_hijos;
            //$insert_padre=$this->_db->query($str);
            if ($insert_hijos && $insert_padre) {
              //$this->_db->commit();
              //$this->_db->autocommit(true);
              $msg_c = "Formulario ingresado con éxito.";
              $msg_co = "success"; // COLOR ALERTA
              $return = '{"msg_c":"' . $msg_c . '","correcto":1,"msg_co":"' . $msg_co . '"}';
            }

            //print_r($data[1]);
            //print_r($campos[2]);
            break;
          case 'una_tabla':
            $str = "INSERT INTO " . $tablas[1] . " (" . $campos[3][0] . ")" . " VALUES(" . $campos[3][1] . ")";
            //echo $str;
            $insert = $this->_db->query($str);
            if ($insert) {
              $msg_c = "Formulario ingresado con éxito.";
              $msg_co = "success"; // COLOR ALERTA
              $return = '{"msg_c":"' . $msg_c . '","correcto":1,"msg_co":"' . $msg_co . '"}';
            }
            break;
          default:
            // code...
            break;
        }
      }
      return $return;
    }

    public function actualizar_formulario($data)
    {
      //print_r($data);
      // TABLAS - VALORES ENCRYP
      // DESENCRYPTAR $data
      // FUNCIONES
      function arr_campos($campos, $db)
      {
        $ids_error = '';
        $data_decode = [];
        $input_multi = [];
        $n_campos = ''; //;
        $v_campos = ''; //;
        $i = 0;
        $input_multi_key = [];
        $campos_multi_key = [];
        $values_multi_key = [];
        //print_r($campos);
        while ($i < count($campos)) { // UNIR VALORES CON SU INFORMACION
          //$decoded_= desencriptar($campos[$i][0],$db);
          $data_decode[$i] = json_decode('{"valor":"' . trim($campos[$i][1]) . '",' . desencriptar($campos[$i][0], $db) . '}');
          //$data_decode[$i]=json_decode('{'.trim($campos[$i][1]).'}');
          //var_dump( is_null($decoded_) );
          if (is_null($data_decode[$i])) {
            echo '{"msg_c":"ERROR EN EL FORMULARIO CAMPO ' . $i . '","correcto":false,"msg_co":"","ids_error":[false]}';
            exit();
          }
          //echo '{'.desencriptar($campos[$i][0],$db).'}';
          //$data_decode[$i]= json_decode('{"valor":"'.trim($campos[$i][1]).'",'.desencriptar($campos[$i][0],$db).'}');

          //print_r($data_decode[$i]);
          //echo trim($campos[$i][1]);
          //echo $data_decode[$i]->tipo;
          if ($data_decode[$i]->rq) {
            if (trim($campos[$i][1]) === "") {
              $ids_error .= '"' . $data_decode[$i]->campo_form . '",';
            }
          }
          if ($data_decode[$i]->tipo == 'multi') {
            //echo $data_decode[$i]->arr;
            $campos_multi_key[$data_decode[$i]->arr] .= $data_decode[$i]->campo . ',';
            $values_multi_key[$data_decode[$i]->arr] .= "'" . $data_decode[$i]->valor . "',";
            //array_push($input_multi[0], $data_decode[$i]);
            //array_push($input_multi[1], $data_decode[$i]);
          }
          if ($data_decode[$i]->tipo == 'normal') {
            $n_campos .= $data_decode[$i]->campo . "='" . $data_decode[$i]->valor . "',";
            //$v_campos.="'".$data_decode[$i]->valor."'".',';
            //echo $data_decode[$i]->valor;
            //$n_campos[ $data_decode[$i]->tabla ].=$data_decode[$i]->campo.',';
            //$v_campos[ $data_decode[$i]->tabla ].="'".$data_decode[$i]->valor."'".',';
          }

          $i++;
        }
        //print_r($n_campos);
        //print_r($v_campos);
        $n_campos = trim($n_campos, ',');
        $v_campos = trim($v_campos, ',');
        $ids_error = '[' . trim($ids_error, ',') . ']';
        //print_r($campos_multi_key);
        //print_r($values_multi_key);
        return [$data_decode, $ids_error, [$campos_multi_key, $values_multi_key], $n_campos];
      }
      //$_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      function desencriptar($cadena, $db)
      {
        $param = str_replace("*", '+', trim($cadena));
        // "SELECT IF( @a:=AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') IS NULL,'0',@a) AS r"
        $str = "SELECT AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') AS r;";
        //$str="SELECT IF( @a:=AES_DECRYPT(FROM_BASE64('$param'),'E>_k&k_<3') IS NULL,'0',@a) AS r";
        $r = $db->query($str);
        $datos = $r->fetch_assoc();
        //echo $datos['r'];
        /*var_dump( is_null($datos['r']) );
        if (is_null($datos['r'])) {
          return '0';
          echo "string";
        }else{
          return $datos['r'];
        }*/
        //Devuelve el string desencriptado
        return $datos['r'];
      }
      // ####################################
      //      DATA[0] - TABLAS
      //      DATA[1] - CAMPOS
      //############## INICIALIZAR LAS TABLAS ######################
      $i = 0;
      $str = []; // CONSULTA ARREGLO
      $v_campos = []; // VALOR DE LOS CAMPOS
      $n_campos = []; // VALOR DE LOS CAMPOS
      $validaciones_error = 0; // VALIDACIONES
      $validaciones_campos = ''; // VALIDACIONES ARRAY
      $tablas = json_decode(desencriptar($data[0], $this->_db));

      //return 1;
      $id_registro = $data[1][0];
      $campos = (arr_campos($data[1][1], $this->_db));
      if (strlen($campos[1]) > 2) {
        //echo "HAY VACIOS";
        $msg_c = "Falta completar datos del Formulario";
        $msg_co = "danger";
        $return = '{"msg_c":"' . $msg_c . '","correcto":false,"msg_co":"' . $msg_co . '","ids_error":[true,' . $campos[1] . ']}';
      } else {
        switch ($tablas[0]) {
          case 'pa_hijos':
            $tabla_padre = $tablas[1];
            //echo "pa_hijos";
            //print_r($campos);
            //print_r($v_campos);

            $str_last_id = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME  = '$tabla_padre'";
            //echo $str_last_id;
            $r = $this->_db->query($str_last_id);
            $datos = $r->fetch_assoc();
            $last_id = $datos['AUTO_INCREMENT'];
            $i_saltos = 0;
            $multi_insert = [];
            sistema . sitraunah . hn / afiliados /
              $tabla_multi = $tablas[3];
            $where_multi = $tablas[4];
            $n_insert = $tablas[5]; // NUMERO DE INSERT
            $campos_row = $tablas[6]; // CAMPOS POR INSERT
            $k = 0;
            //echo "soy campos row ".$campos_row;
            $multi_str = '';
            $tabla = $tablas[3];
            while ($k < $n_insert) { // CANTIDAD DE CONSULAS A REALIZAR (TABLAS) INICIALIZAR KEYS
              //echo $tablas[4];
              //$k2=0;
              $multi_str .= "INSERT INTO " . $tabla . "(" . $tablas[4] . ',' . trim($campos[2][0][$k], ',') . ") VALUES ('" . $mysqli->real_escape_string($last_id) . "'," . trim($campos[2][1][$k], ',') . ");";
              //echo $campos[2][0][$k];
              //echo $campos[2][1][$k];
              $k++;
            }
            //$this->_db->autocommit(false);
            //echo $multi_str;
            $str = "INSERT INTO " . $tablas[1] . " (" . $campos[3][0] . ")" . " VALUES(" . $campos[3][1] . ")";
            //echo $str;            //
            //print_r($campos[3]);
            $insert_padre = $this->_db->query($str);
            $insert_hijos = $this->_db->multi_query($multi_str);
            //echo $insert_padre;
            //echo $insert_hijos;
            //$insert_padre=$this->_db->query($str);
            if ($insert_hijos && $insert_padre) {
              //$this->_db->commit();
              //$this->_db->autocommit(true);
              $msg_c = "Formulario ingresado con éxito.";
              $msg_co = "success"; // COLOR ALERTA
              $return = '{"msg_c":"' . $msg_c . '","correcto":1,"msg_co":"' . $msg_co . '"}';
            }

            //print_r($data[1]);
            //print_r($campos[2]);
            break;
          case 'una_tabla':
            //$str="UPDATE $str="INSERT "" INTO ".$tablas[1]." (".$campos[3][0].")"." VALUES(".$campos[3][1].")";
            //echo $str;
            $str_primary = "SHOW KEYS FROM " . $tablas[1] . " WHERE Key_name = 'PRIMARY'";
            $r_key = $this->_db->query($str_primary);
            $datos_key = $r_key->fetch_assoc();
            $campo_where = $datos_key['Column_name'];
            $str = "UPDATE " . $tablas[1] . " SET " . $campos[3] . " WHERE " . $campo_where . "='" . $id_registro . "'";
            //$return = $str;
            //$return =print_r($campos[3]);
            /*if (@$tablas[3]!='') {
          //echo $last_id;

          // BORRAR TODO SI LO HAY
          $str_delete="DELETE FROM ventas_man_categorias_productos WHERE fk_producto='$id_registro'";
          //echo $str_delete;
          $n_data=json_decode(base64_decode($data[2][0]));
          //print_r($n_data);
          $i=0;
          $str_values="";
          while ($i < count($n_data)) {
            $str_values.="('".$id_registro."','".$n_data[$i]."'),";
            $i++;
          }
          $str_values= @trim($str_values, ',');
          $insert2=$this->_db->query( $tablas[3].' '.$str_values );
        }*/

            //echo @$tablas[2].' '.$str_values;
            $update = $this->_db->query($str);
            //echo $str;
            if ($update) {
              $msg_c = "Formulario actualizado con éxito.";
              $msg_co = "success"; // COLOR ALERTA
              $return = '{"msg_c":"' . $msg_c . '","correcto":1,"msg_co":"' . $msg_co . '"}';
            }
            break;
          default:
            // code...
            break;
        }
      }
      return $return;
    }

    public function get_info_user($us)
    {
      $str = "SELECT CONCAT('[[\"',nombre_usuario,'\",',id_usuario,'],\"',centro_regional,'\"]') AS r FROM man_usuarios WHERE nombre_usuario='$us'";
      //echo $str;
      $r = $this->_db->query($str); // HACER LA CONSULTA
      $datos = $r->fetch_assoc();
      return $datos['r']; // RESPUESTA
    }
    public function get_access($us)
    {
      $str_id = "SELECT id_usuario FROM man_usuarios WHERE nombre_usuario='$us'";
      $r_id = $this->_db->query($str_id);
      $datos1 = $r_id->fetch_assoc();
      $id = $datos1['id_usuario'];
      // ARREGLO PADRE
      $str_arr = "SELECT  CONCAT('[',GROUP_CONCAT(DISTINCT id_man_global_menu),']') AS arr_padres
	        FROM `man_user_menu`
	        JOIN man_pest_menu ON(fk_pest=id_man_pest_menu)
	        JOIN  man_menus ON(fk_menu=id_man_menus)
	        JOIN  man_global_menu ON(fk_padre=id_man_global_menu) WHERE fk_user=$id AND estado=1";
      $r_arr = $this->_db->query($str_arr); // HACER LA CONSULTA
      $datos3 = $r_arr->fetch_assoc();
      $arr_padres = json_decode($datos3['arr_padres']);
      $i = 0;
      $menu_global = '{';
      while ($i < count($arr_padres)) {
        $registro1 = $arr_padres[$i];
        $str_din = "SELECT n_var_json,CONCAT('[\"',nombre_menu,'\",\"',icono,'\",\"',comentario,'\"]') AS arr FROM man_global_menu WHERE id_man_global_menu=$registro1";
        $r_din = $this->_db->query($str_din);
        $datos_din = $r_din->fetch_assoc();
        //echo $datos_din['arr'];
        //echo $datos_din['n_var_json'];
        $menu_global .= '"' . $datos_din['n_var_json'] . '":[' . $datos_din['arr'] . ',[';
        $str_menu = "SELECT CONCAT('[',GROUP_CONCAT(DISTINCT id_man_menus),']') AS arr_menus
		        FROM `man_user_menu`
		        JOIN man_pest_menu ON(fk_pest=id_man_pest_menu)
		        JOIN  man_menus ON(fk_menu=id_man_menus)
	        	JOIN  man_global_menu ON(fk_padre=id_man_global_menu) WHERE fk_user=$id AND id_man_global_menu=$registro1";
        $r_menu = $this->_db->query($str_menu);
        $datos_menu = $r_menu->fetch_assoc();
        $arr_menus = json_decode($datos_menu['arr_menus']);
        $j = 0;
        $menu = '';
        while ($j < count($arr_menus)) {
          $registro2 = $arr_menus[$j];
          $str_encabezado = "SELECT CONCAT('[[\"',nombre_menu,'\",\"',codigo_,'\",\"',icono_class,'\",\"',menu_url,'\"]') AS v_encabezado_menu
			           FROM man_menus WHERE id_man_menus=$registro2";
          $r_encabezado = $this->_db->query($str_encabezado);
          $datos_encabezado = $r_encabezado->fetch_assoc();
          $v_encabezado_menu = $datos_encabezado['v_encabezado_menu'];
          $str_v_pest = "SELECT CONCAT('[',GROUP_CONCAT(CONCAT('[\"',nombre_pestaña,'\",\"',url_archivo,'\",\"',id_,'\"]') ORDER BY posicion ASC) ,']]') AS v_pest_menu
			           FROM `man_user_menu` JOIN man_pest_menu ON (fk_pest=id_man_pest_menu) WHERE fk_user=$id AND fk_menu=$registro2 AND estado=1;";
          $r_v_pest = $this->_db->query($str_v_pest);
          $datos_v_pest = $r_v_pest->fetch_assoc();
          $v_pest_menu = $datos_v_pest['v_pest_menu'];
          $menu .= $v_encabezado_menu . ',' . $v_pest_menu . ',';
          $j++;
        }

        //print_r($arr_menus);
        $menu = trim($menu, ',') . ']]';
        $menu_global .= $menu . ',';
        $i++;
      }
      return  base64_encode(trim($menu_global, ',') . '}');
    }
    public function get_us_valido($us, $p)
    {
      $str = "SELECT REPLACE(fn_valida_usuario2('$us','$p'),'\n','') AS r";
      $str2 = "SELECT centro_regional FROM man_usuarios WHERE nombre_usuario='$us''"; // PARA TRAER EL NUMERO DE SECCIONAL
      //echo $str;
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
    public function set_reset_pass($us, $cod)
    {
      $str = "SELECT fn_reset_us('$us', '$cod') AS r;";
      $r = $this->_db->query($str); // HACER LA CONSULTA
      $datos = $r->fetch_assoc();
      return $datos['r'];
    }
    public function get_row($data)
    {
      function desencriptar($cadena)
      {
        $__K_K = '__K&K';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($__K_K), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($__K_K))), "\0");
        return $decrypted;  //Devuelve el string desencriptado
      }
      //print_r($data);
      $str = json_decode(desencriptar($data[1]));
      if (!json_last_error()) {
        $q = str_replace("?", $data[0], $str[0]);
        $r = $this->_db->query($q);
        $fila = $r->fetch_array(MYSQLI_NUM);
        return '{"correcto":true,"valor":"' . $fila[0] . '"}';
      } else {
      }
      //echo json_last_error();
      //print_r($str);
    }
    public function get_b_dinamica($query_encode, $query)
    {
      function desencriptar($cadena)
      {
        $__K_K = '__K&K';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($__K_K), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($__K_K))), "\0");
        return $decrypted;  //Devuelve el string desencriptado
      }
      $str = json_decode(desencriptar($query_encode));
      if (!json_last_error()) {
        $q = str_replace("?", $query, $str[0]);
        $r = $this->_db->query($q);
        //$fila=$r->fetch_array(MYSQLI_NUM);
        $cadena_json = '';
        while ($fila = $r->fetch_array(MYSQLI_NUM)) {
          $cadena_json .= '{"value":"' . $fila[1] . '","v":"' . $fila[0] . '"},';
        }
        //$cadena_json.=trim($cadena_json, ',').']}';
        return '{"suggestions": [' . trim($cadena_json, ',') . ']}';
      } else {
      }
    }
    public function get_tabla($data)
    {
      // - ROWS O TOTAL $data[0] AHORA YA NO IRA
      // - CAMPOS $data[1][i]
      // - VISTA $data[2]
      // - PAGINACION (inicio - fin)
      //     $data[3][0]  comienzo
      //     $data[3][1]  limite de registros
      // - WHERES CON FILTROS O SIN FILTROS $data[4]
      // - WHERES ESPECIALES $data[6]
      // - ORDER BY $data[7]
      $i = 0;
      $i2 = 1;
      switch ($data[0]) {
        case 'rows':
          $pag = '';
          if ($data[3]) {
            $pag = ' LIMIT ' . $data[3][0] . ',' . $data[3][1];
          }
          //$pag=$data[3][0].','.$data[3][1];
          //$t=$data[3][1]-$data[3][0];
          $campos = '';
          while ($i < count($data[1])) {
            $campos .= $data[1][$i] . ',';
            $i++;
          }
          $campos = trim($campos, ',');
          $where = '';
          $selects_return = '';
          if (@$data[4]) {
            $i = 0;
            while ($i < count($data[4])) { // FILTROS HIJOS
              $arr_select = '';
              $data_row = $data[4][$i];
              $fk = $data_row[0];
              $nombre = $data_row[1];
              $def_name = $data_row[2][0];
              $def_val = $data_row[2][1];
              $n_filtro = '"' . $data_row[3] . '",';
              $hay_set = 0;
              $opt_select = 0;
              //echo $def_name;
              if (gettype($def_name) == 'string') {
                $wk = '';
                if ($data[6]) {
                  $wk .= "AND " . $data[6];
                }
                $str1 = "SELECT '$def_val' AS 'A', '$def_name' AS 'B'";
                $str2 = "SELECT DISTINCT($fk) AS A, $nombre AS B FROM " . $data[2] . " WHERE 1 " . $wk . " ORDER BY " . $fk;
                //echo $str;
                $r1 = $this->_db->query($str1);
                $r2 = $this->_db->query($str2);
                while ($fila1 = $r1->fetch_assoc()) {
                  $arr_select .= '["' . $fila1['A'] . '","' . $fila1['B'] . '"],';
                }
                while ($fila2 = $r2->fetch_assoc()) {
                  $arr_select .= '["' . $fila2['A'] . '","' . $fila2['B'] . '"],';
                }
              }
              if (gettype($def_name) == 'boolean') {
                $hay_set = 1;
                $str = "SELECT DISTINCT($fk) AS 'A', $nombre AS 'B' FROM " . $data[2] . " ORDER BY " . $fk;
                // EXISTE EL PARAMETRO DENTRO DE LOS RESULTADOS ESPECIDICADOS
                // DE NO EXISTIR ESTE PARAMETRO ES QUE NO SE DEBE MOSTRAR EN PANTALLA
                $r = $this->_db->query($str);
                //print_r($this->_db);
                //echo $str;
                $def_valpao = $def_val;
                $eciste_param = false;
                while ($fila = $r->fetch_assoc()) {
                  //echo ($def_val==$fila['A']);
                  //echo $def_val.'/'.$fila['A'].'('.($def_val==$fila['A']).')';
                  if ($def_val == $fila['A']) {
                    if (gettype($def_val) == "string") {
                      $def_val = '"' . $def_val . '"';
                    }
                    $opt_select = $def_val;
                    $eciste_param = true;
                  }
                  $arr_select .= '["' . $fila['A'] . '","' . $fila['B'] . '"],';
                }
                //echo "=> ".$arr_select;
                if ($eciste_param) {
                  $where .= "AND " . $fk . "='" . $def_valpao . "' ";
                }
              }

              //$campos.=$data[1][$i].',';
              $arr_select = '["' . $fk . '",' . $n_filtro . $hay_set . ',' . $opt_select . ',' . '[' . trim($arr_select, ',') . ']]';
              $selects_return .= $arr_select . ',';
              $i++;
            }
            $selects_return = '[' . trim($selects_return, ',') . ']';

            //echo $selects_return;


            //$where='AND '.$data[4];
          }

          if ($data[5]) {
            $i2 = 0;
            $a = '';
            while ($i2 < count($data[5])) {
              if ($data[5][$i2][0]) {
                $where .= "AND " . $data[5][$i2][0] . "='" . $data[5][$i2][1] . "' ";
              }
              $i2++;
            }
          }
          if ($data[6]) {
            $where .= "AND " . $data[6];
          }
          $order = '';
          $str = "SELECT $campos FROM " . $data[2] . " WHERE 1 " . $where . "$pag";
          if ($data[7]) {
            $order .= $data[7];
            $str = "SELECT $campos FROM " . $data[2] . " WHERE 1 " . $where . " ORDER BY " . $order . $pag;
          }
          //echo $a;

          //echo $str;
          $r = $this->_db->query($str); // HACER LA CONSULTA
          $str2 = "SELECT COUNT(*) AS t FROM " . $data[2] . " WHERE 1 " . $where;
          //echo  $str;
          $r2 = $this->_db->query($str2);
          $fila = $r2->fetch_assoc();
          $t_db = $fila['t']; // TOTAL DB
          $t = $r->num_rows;
          $a = json_encode($data);
          $cadena_json = '{"correcto":true,"total":' . $t . ',"a":' . $a . ',"str":"' . $str . '","total_db":' . $t_db . ',"registros":[';
          $i = 0;
          while ($fila = $r->fetch_assoc()) {  // TOTAL FILAS
            $row = '[';
            while ($i < count($data[1])) {
              $row .= '"' . $fila[$data[1][$i]] . '",';
              $i++;
            }
            $row = trim($row, ',') . ']';
            $i = 0;
            $cadena_json .= $row . ',';
            $i2++;
          }
          //$str = trim($str, ',');$str.=" FROM ".$data[2];
          if ($data[4]) {
            $cadena_json = trim($cadena_json, ',') . '],"select":' . $selects_return . '}';
          } else {
            $cadena_json = trim($cadena_json, ',') . ']}';
          }
          return $cadena_json;
          break;

        case 'row':
          $campos = '';
          while ($i < count($data[1])) {
            $campos .= $data[1][$i] . ',';
            $i++;
          }
          $where = $data[1][0];
          $campos = trim($campos, ',');
          $str = "SELECT $campos FROM " . $data[2] . " WHERE $where=" . $data[3];
          $r = $this->_db->query($str);
          //echo $str;
          if ($r) {
            $i = 0;
            while ($fila = $r->fetch_assoc()) {  // TOTAL FILAS
              $row = '[';
              while ($i < count($data[1])) {
                $row .= '"' . $fila[$data[1][$i]] . '",';
                $i++;
              }
              $row = trim($row, ',') . ']';
              $i = 0;
              //$cadena_json.=$row.',';
            }

            $return = '{"correcto":true,"data":' . $row . '}';
          } else {
            $return = '{"correcto":false}';
          }

          //return $cadena_json;
          return $return;
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
