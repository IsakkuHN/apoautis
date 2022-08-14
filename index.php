<?php
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
require 'Slim/Slim.php';
require_once 'JWT.php';
require_once 'BeforeValidException.php';
require_once 'ExpiredException.php';
require_once 'SignatureInvalidException.php';
require_once 'v1/index.php';
session_start();
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
use \Firebase\JWT\JWT;
define("KEY","59bc4655e7c71571eeb2f7e9cfb176aaaae112eedda8292d45c9c856c998fc8f");
$_SESSION["nombre_usuario"]='';
$app->post('/fotografia/', function () use ($app) {
/*$app->response->headers->set('Content-Type', 'application/json');
$pngUrl=$app->request->post('pngUrl');
$n_emp=$app->request->post('n_emp');
$user=$app->request->post('user');*/
//echo $n_emp;// ES PNG

$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$pngUrl=$data['obj'];
$n_emp=$data['n_emp'];


$formato='';
if (strpos($pngUrl, 'image/png') !== false) {
    //$formato='png';
    $n_b64 = str_replace("data:image/png;base64,","",$pngUrl);
    //echo $n_b64;
    //$path = "../assets/_images/$n_emp.png";
    $path = "../assets/_images/".$n_emp.".png";
    file_put_contents($path,base64_decode($n_b64));
}
if (strpos($pngUrl, 'image/jpeg') !== false) {
    //$formato='jpeg';
    $n_b64 = str_replace("data:image/jpeg;base64,", "",$pngUrl);
    $path = "../assets/jpgs/".$n_emp.".jpeg";

    //$image = imagecreatefrompng(base64_decode($n_b64));
    //imagepng($image, $new_filename);
    file_put_contents($path,base64_decode($n_b64));
    //(imagecreatefromstring(file_get_contents("../assets/jpgs/".$n_emp.".jpeg")), "output.png");

    $f = imagecreatefromjpeg($path);
    imagepng($f, "../assets/_images/".$n_emp.".png");
}

	//echo $n_b64;
//file_put_contents($path,base64_decode($n_b64));
// INGRESAR EN LA BASE DE DATOS QUIEN GUARDO LA FOTO
  include_once 'consultas.class.php';
  $instancia_db = new DbModelo();
  $reset_pass=$instancia_db->set_guardar_imagen($n_emp,$user);

  if ($reset_pass) {
    echo $reset_pass;
  }
//echo  1;
//echo  $pngUrl;
//echo  $n_emp;
//echo  $user;
});

$app->post('/fotografia_blog/', function () use ($app) {
/*$app->response->headers->set('Content-Type', 'application/json');
$pngUrl=$app->request->post('pngUrl');
$n_emp=$app->request->post('n_emp');
$user=$app->request->post('user');*/
//echo $n_emp;// ES PNG

$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$pngUrl=$data['obj'][0];
$cod=$data['cod'];
//$_db = new mysqli('LOCALHOST', 'sitrauna_user', '-kD3S6kq0cNv', 'sitrauna_db');
//$_db->set_charset("utf8");
echo $pngUrl;
$formato='';
if (strpos($pngUrl, 'image/png') !== false) {
    //$formato='png';
    $n_b64 = str_replace("data:image/png;base64,","",$pngUrl);
    //echo $n_b64;
    //$path = "../assets/_images/$n_emp.png";
    $path = "../assets/_images_blog/".$cod.".png";
    file_put_contents($path,base64_decode($n_b64));
}
if (strpos($pngUrl, 'image/jpeg') !== false) {
    //$formato='jpeg';
    $n_b64 = str_replace("data:image/jpeg;base64,", "",$pngUrl);
    $path = "../assets/jpgs_blog/".$cod.".jpeg";

    //$image = imagecreatefrompng(base64_decode($n_b64));
    //imagepng($image, $new_filename);
    file_put_contents($path,base64_decode($n_b64));
    //(imagecreatefromstring(file_get_contents("../assets/jpgs/".$n_emp.".jpeg")), "output.png");

    $f = imagecreatefromjpeg($path);
    imagepng($f, "../assets/_images_blog/".$cod.".png");
}

	//echo $n_b64;
//file_put_contents($path,base64_decode($n_b64));
// INGRESAR EN LA BASE DE DATOS QUIEN GUARDO LA FOTO
  /*include_once 'consultas.class.php';
  $instancia_db = new DbModelo();
  $reset_pass=$instancia_db->set_guardar_imagen($n_emp,$user);

  if ($reset_pass) {
    echo $reset_pass;
  }*/
//echo  1;
//echo  $pngUrl;
//echo  $n_emp;
//echo  $user;
});

$app->post('/fotografia_clinica/', function () use ($app) {
/*$app->response->headers->set('Content-Type', 'application/json');
$pngUrl=$app->request->post('pngUrl');
$n_emp=$app->request->post('n_emp');
$user=$app->request->post('user');*/
//echo $n_emp;// ES PNG

$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$pngUrl=$data['obj'];
$n_emp=$data['n_emp'];


$formato='';
if (strpos($pngUrl, 'image/png') !== false) {
    //$formato='png';
    $n_b64 = str_replace("data:image/png;base64,","",$pngUrl);
    //echo $n_b64;
    //$path = "../assets/_images/$n_emp.png";
    $path = "../assets/_images_clinica/".$n_emp.".png";
    file_put_contents($path,base64_decode($n_b64));
}
if (strpos($pngUrl, 'image/jpeg') !== false) {
    //$formato='jpeg';
    $n_b64 = str_replace("data:image/jpeg;base64,", "",$pngUrl);
    $path = "../assets/jpgs_clinica/".$n_emp.".jpeg";

    //$image = imagecreatefrompng(base64_decode($n_b64));
    //imagepng($image, $new_filename);
    file_put_contents($path,base64_decode($n_b64));
    //(imagecreatefromstring(file_get_contents("../assets/jpgs/".$n_emp.".jpeg")), "output.png");

    $f = imagecreatefromjpeg($path);
    imagepng($f, "../assets/_images_clinica/".$n_emp.".png");
}

	//echo $n_b64;
//file_put_contents($path,base64_decode($n_b64));
// INGRESAR EN LA BASE DE DATOS QUIEN GUARDO LA FOTO
  include_once 'consultas.class.php';
  $instancia_db = new DbModelo();
  $reset_pass=$instancia_db->set_guardar_imagen($n_emp,$user);

  if ($reset_pass) {
    echo $reset_pass;
  }
//echo  1;
//echo  $pngUrl;
//echo  $n_emp;
//echo  $user;
});

$app->post('/fotografia_escuela/', function () use ($app) {
/*$app->response->headers->set('Content-Type', 'application/json');
$pngUrl=$app->request->post('pngUrl');
$n_emp=$app->request->post('n_emp');
$user=$app->request->post('user');*/
//echo $n_emp;// ES PNG

$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$pngUrl=$data['obj'];
$n_emp=$data['n_emp'];

echo $n_emp;
$formato='';
if (strpos($pngUrl, 'image/png') !== false) {
    //$formato='png';
    $n_b64 = str_replace("data:image/png;base64,","",$pngUrl);
    //echo $n_b64;
    //$path = "../assets/_images/$n_emp.png";
    $path = "../assets/_images_escuela/".$n_emp.".png";
    file_put_contents($path,base64_decode($n_b64));
}
if (strpos($pngUrl, 'image/jpeg') !== false) {
    //$formato='jpeg';
    $n_b64 = str_replace("data:image/jpeg;base64,", "",$pngUrl);
    $path = "../assets/jpgs_escuela/".$n_emp.".jpeg";

    //$image = imagecreatefrompng(base64_decode($n_b64));
    //imagepng($image, $new_filename);
    file_put_contents($path,base64_decode($n_b64));
    //(imagecreatefromstring(file_get_contents("../assets/jpgs/".$n_emp.".jpeg")), "output.png");

    $f = imagecreatefromjpeg($path);
    imagepng($f, "../assets/_images_escuela/".$n_emp.".png");
}

	//echo $n_b64;
echo  1;
//echo  $pngUrl;
//echo  $n_emp;
//echo  $user;
});


$app->post('/archivo/', function () use ($app) {
$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$data_url=$data['obj'][0];
$nombre=$data['obj'][1];
$tipo=$data['obj'][2];
$peso=$data['obj'][3];
$n_solicitud=$data['n_solicitud'];
$informacion_documento_=$data['informacion'];
$instancia=$data['instancia'];
$ingresado_por=$data['ingresado_por'];
$cod=$data['cod'];

switch ($tipo) {
  case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    $ext=".docx";
    break;

  case 'application/msword':
    $ext=".doc";
   break;

  case 'application/pdf':
    $ext=".pdf";
   break;

  case 'image/jpeg':
   $ext=".jpg";
  break;

  case 'image/png':
   $ext=".png";
  break;
  case 'text/plain':
   $ext=".txt";
  break;
  default:
    // code...
    break;
}

$str="INSERT INTO `documentos_problematica`(fk_solicitud,nombre_documento,nombre_aleatorio,tipo,tipo_corto,peso_kb,informacion_documento,numero_instancia,ingresado_por)
VALUES('$n_solicitud','$nombre','$cod','$tipo','$ext','$peso','$informacion_documento_','$instancia','$ingresado_por')";
//echo $str;

$_db = new mysqli('LOCALHOST', 'sitrauna_user', '-kD3S6kq0cNv', 'sitrauna_db');
$_db->set_charset("utf8");

$r = $_db->query($str);
if ($r) {

  $n_b64 = str_replace("data:".$tipo.";base64,","",$data_url);
  $path = "../assets/documentos/".$cod.$ext;
  file_put_contents($path,base64_decode($n_b64));
  if (file_exists($path)) {
    echo '{"correcto":true,"msg":"Archivo guardado correctamente."}';
}
  //$path = "../assets/documentos/".$cod.$ext;
  //file_put_contents($path,base64_decode($data_url));
 //upload_file($data_url,$ext);
}
//$path = "../assets/documentos/".$nombre;
//file_put_contents($path,base64_decode($data_url));

});

$app->post('/archivo_clinica/', function () use ($app) {
$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$data_url=$data['obj'][0];
$nombre=$data['obj'][1];
$tipo=$data['obj'][2];
$peso=$data['obj'][3];
$fk_empleado=$data['fk_empleado'];
$informacion_documento_=$data['informacion'];
$ingresado_por=$data['ingresado_por'];
$cod=$data['cod'];

switch ($tipo) {
  case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    $ext=".docx";
    break;

  case 'application/msword':
    $ext=".doc";
   break;

  case 'application/pdf':
    $ext=".pdf";
   break;

  case 'image/jpeg':
   $ext=".jpg";
  break;

  case 'image/png':
   $ext=".png";
  break;
  case 'text/plain':
   $ext=".txt";
  break;
  default:
    // code...
    break;
}

$str="INSERT INTO `clinica_documentos_pacientes`(fk_paciente,nombre_documento,nombre_aleatorio,tipo,tipo_corto,peso_kb,informacion_documento,ingresado_por)
VALUES('$fk_empleado','$nombre','$cod','$tipo','$ext','$peso','$informacion_documento_','$ingresado_por')";
//echo $str;

$_db = new mysqli('LOCALHOST', 'sitrauna_user', '-kD3S6kq0cNv', 'sitrauna_db');
$_db->set_charset("utf8");

$r = $_db->query($str);
if ($r) {

  $n_b64 = str_replace("data:".$tipo.";base64,","",$data_url);
  $path = "../assets/documentos_clinica/".$cod.$ext;
  file_put_contents($path,base64_decode($n_b64));
  if (file_exists($path)) {
    echo '{"correcto":true,"msg":"Archivo guardado correctamente."}';
}
  //$path = "../assets/documentos/".$cod.$ext;
  //file_put_contents($path,base64_decode($data_url));
 //upload_file($data_url,$ext);
}
//$path = "../assets/documentos/".$nombre;
//file_put_contents($path,base64_decode($data_url));

});

$app->post('/archivo_escuela/', function () use ($app) {
$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
$json = $app->request->getBody();
$data = json_decode($json, true);
$data_url=$data['obj'][0];
$nombre=$data['obj'][1];
$tipo=$data['obj'][2];
$peso=$data['obj'][3];
$fk_alumno=$data['fk_alumno'];
$nivel=$data['nivel'];
$tipo_documento=$data['tipo_documento'];
$informacion_documento_=$data['informacion'];
$ingresado_por=$data['ingresado_por'];
$cod=$data['cod'];

switch ($tipo) {
  case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
    $ext=".docx";
    break;

  case 'application/msword':
    $ext=".doc";
   break;

  case 'application/pdf':
    $ext=".pdf";
   break;

  case 'image/jpeg':
   $ext=".jpg";
  break;

  case 'image/png':
   $ext=".png";
  break;
  case 'text/plain':
   $ext=".txt";
  break;
  default:
    // code...
    break;
}

$str="INSERT INTO `escuela_documentos_alumnos`(fk_alumno,nivel,tipo_documento,nombre_documento,nombre_aleatorio,tipo,tipo_corto,peso_kb,informacion,ingresado_por)
VALUES('$fk_alumno','$nivel','$tipo_documento','$nombre','$cod','$tipo','$ext','$peso','$informacion_documento_','$ingresado_por')";
//echo $str;

$_db = new mysqli('LOCALHOST', 'sitrauna_user', '-kD3S6kq0cNv', 'sitrauna_db');
$_db->set_charset("utf8");

$r = $_db->query($str);
if ($r) {

  $n_b64 = str_replace("data:".$tipo.";base64,","",$data_url);
  $path = "../assets/documentos_escuela/".$cod.$ext;
  file_put_contents($path,base64_decode($n_b64));
  if (file_exists($path)) {
    echo '{"correcto":true,"msg":"Archivo guardado correctamente."}';
}
  //$path = "../assets/documentos/".$cod.$ext;
  //file_put_contents($path,base64_decode($data_url));
 //upload_file($data_url,$ext);
}
//$path = "../assets/documentos/".$nombre;
//file_put_contents($path,base64_decode($data_url));

});

$app->get('/login/reset_pass/:cadena', function ($cadena) use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $d=(json_decode(base64_decode($cadena)));
   $reset_pass=$instancia_db->set_reset_pass($d[0],$d[1]);
   $app->response->headers->set('Content-Type', 'application/json');
   $app->response->setStatus(200);
   echo $reset_pass;
});

$app->get('/login/:cadena', function ($cadena) use ($app) { // {"msg":"Usuario o Cotraseña Incorrecta","correcto":0}

    //echo base64_decode($cadena);
    $app->response->headers->set('Content-Type', 'application/json');
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $app->response->setStatus(200);
        //print_r($instancia_db);
        $d=(json_decode(base64_decode($cadena)));
        $_SESSION["nombre_usuario"]=$d[0];
        $json_us_valido=$instancia_db->get_us_valido($d[0],$d[1]);
         //print_r($json_us_valido);
         $ar=json_decode($json_us_valido);
         //echo $ar['correcto'];
        //echo $ar->correcto;
        date_default_timezone_set('America/Tegucigalpa');
        if ($ar->correcto) { // USUARIO CORRECTO
            // GET ACCESS
            $access=$instancia_db->get_access($d[0]);
            //echo $access;
            $unico=md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);     // md5(get_client_ip().$_SERVER['HTTP_USER_AGENT'])
            $token = array(
            "iss" => "http://sistema.sitraunah.hn/_api_sitraunah/login/",
            "us" => $d[0],
            "cot"=>$unico,
            "iat" => time()// FECHA DE CREACION SU VENCIMIENTO SERA 1800 FECHA VENCIMIENTO   http://localhost/sistema_ipsd/login/  echo '{"data":'.$json_us_valido.'}';
            );
           $jwt = JWT::encode($token, KEY);
           echo '{"data":'.$json_us_valido.',"token":"'.$jwt.'","url":"http://sistema.sitraunah.hn/_api_ipsd/login/","unico":"'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].'","access":"'.$access.'"}';
           $app->response->setStatus(200);
           //header("Location: http://www.example.com/");
        }else{
           echo '{"data":'.$json_us_valido.'}';
        }
    //echo $_SERVER['HTTP_REFERER'];



    //echo $app->request()->headers()->all();
});

$app->post('/login/reset_pass/', function () use ($app) { // {"msg":"Usuario o Cotraseña Incorrecta","correcto":0}
  include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type', 'application/json');
   $data = base64_decode( $app->request->post('data') );
   $data = json_decode( $data );
   $ingreso_formulario=$instancia_db->reset_pass($data);
   echo $ingreso_formulario;
});

$app->get('/login/validar_token/:token', function ($token) use ($app) {
      date_default_timezone_set('America/Tegucigalpa');
      //######################################
      //echo 'ALGO';
      try {
                $decoded = JWT::decode($token, KEY, array('HS256'));
                //print_r($decoded);
                $x=1;
                if( ( ($decoded->iat+900)>time() ) && ($x)){
                   // CREAR EL TOKEN NUEVO
                   $token = array(
                   "iss" => "http://sistema.sitraunah.hn/_api_sitraunah/login/", //http://localhost/api_ipsd/login/
                   "us" => $decoded->us,
                   "cot"=>$decoded->cot,
                   "iat" => time());
                   $jwt = JWT::encode($token, KEY);
                   echo '{"correcto":true,"token":"'.$jwt.'","user":"'.$decoded->us.'->'.(($decoded->iat)+1200).'"}';
                }else{
                    echo '{"correcto":false,"msg":"Token Invalidos"'.($decoded->iat).'}'.$app->request()->headers('TOKEN');
                }
    } catch (Exception $e) {
        echo $e.'{"correcto":false,"msg":"Token Invalidoo"}';
    }
      //######################################
 });

 $app->get('/simple_validar_token/:token', function ($token) use ($app) {
    //######################################
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    if ($app->request()->headers('TOKEN')=='45d38a') {
      try {
          $decoded = JWT::decode($token, KEY, array('HS256'));
          echo '{"correcto":true}';
      } catch (Exception $e) {
          echo '{"correcto":false}';
      }
    }else{
      echo '{"correcto":false}';
    }
    //######################################
  });

$app->get('/login/validar_tokennnn/:token', function ($token) use ($app) {
    try {
         if (( ($app->request()->headers('TOKEN')) && ($app->request()->headers('TOKEN')=='45d38a') )  ) {
                //echo 'Mediante cURL '.$app->request()->headers('AGENTE'); // AGENTE DE EL cURL     || 1
                //echo "<hr>";
          // (( ($app->request()->headers('TOKEN')) && ($app->request()->headers('TOKEN')=='45d38a') )  )
      //######################################
      try {
                $decoded = JWT::decode($token, KEY, array('HS256'));
                $token = array(
                "iss" => "http://localhost/_api_emc/login/", //http://localhost/api_ipsd/login/
                "us" => $decoded->us,
                "cot"=>$decoded->cot,
                "iat" => (time()-21600));// FECHA DE CREACION SU VENCIMIENTO SERA 1800 FECHA VENCIMIENTO
                $jwt = JWT::encode($token, KEY);
                $x=0;
                if( ($decoded->cot) === (md5($_SERVER['REMOTE_ADDR'].$app->request()->headers('AGENTE'))) ){
                  $x=1;
                };
                //echo "<hr>";   ($decoded->cot) === (md5(get_client_ip().$_SERVER['HTTP_USER_AGENT']))
                //echo get_client_ip().$_SERVER['HTTP_USER_AGENT'].'-> '.md5(get_client_ip().$_SERVER['HTTP_USER_AGENT']);
                //echo get_client_ip().$app->request()->headers('AGENTE').'-> '.md5(get_client_ip().$app->request()->headers('AGENTE'));
                //echo '<hr>';
                //echo $decoded->cot.'-> '.md5($decoded->cot);
                //echo '<hr>';
                //echo $x;
               // echo (md5(get_client_ip().$app->request()->headers('AGENTE')));
                if ( ($x) && ( (($decoded->iat)+1200)>(time()-21600) ) ) {
                 echo '{"correcto":true,"token":"'.$jwt.'","user":"'.$decoded->us.'"}';
                }else{echo '{"correcto":false,"msg":"Token Invalidos"}';}
    } catch (Exception $e) {
        echo $e.'{"correcto":false,"msg":"Token Invalidoo"}';
    }
      //######################################

        }else{$app->response->setStatus(401);}
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(401);
    }
 });

 $app->get('/saludar/:str', function ($str) use ($app) {
   echo 10+$str;
});

 $app->get('/get_tabla/:str', function ($str) use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
   $data = base64_decode( $str );
   $data = json_decode( $data );

   $get_tabla=$instancia_db->get_tabla($data);
   //print_r($data);
   echo $get_tabla;
   // VALIDAR TOKEN
   /*if (isset($_COOKIE["token"])&&$_COOKIE["token"]!='') {
     $token= json_decode(file_get_contents('https://sistema.sitraunah.hn/_api_sitraunah/login/validar_token/'.$_COOKIE["token"]));
     //print_r($token);
     //echo $token->correcto;
     if ($token->correcto) {
       $data = base64_decode( $str );
       $data = json_decode( $data );

       $get_tabla=$instancia_db->get_tabla($data);
       //print_r($data);
       echo $get_tabla;
     }else{
       // TOKEN INCORRECTO
       echo '{"correcto":false,"login_expire":true}';
     }
   }else{
     echo '{"correcto":false,"login_expire":true}';
   }*/

   //echo $str;
});

$app->get('/get_row/:str', function ($str) use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
   $data = base64_decode( $str );
   $data = json_decode( $data );
   $get_row=$instancia_db->get_row($data);
   //print_r($data);
   echo $get_row;
   //echo $str;
});

$app->get('/get_b_dinamica/:str', function ($str) use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type','application/json;charset=utf-8');
   $query_db = base64_decode( $str );
   //$data = json_decode( $data );
   $query=$_GET['query'];
   //echo $query;
   $get_busqueda=$instancia_db->get_b_dinamica($query_db,$query);
   echo $get_busqueda;
});

 $app->get('/get_pdf/:str', function ($str) use ($app) {
   include_once 'get_pdf.class.php'; //incluimos la libreria
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $instancia_pdf = new pdfModelo();
   //$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
   $data = json_decode(base64_decode( $str ));
   //$data = json_decode( $data );
   $get_tabla=$instancia_db->get_tabla( json_decode(base64_decode( $data[0] )) );
   //$get_tabla=json_decode($get_tabla);//
   //print_r ( (base64_decode( $data[0] )) );
   //echo $get_pdf;
   $get_tabla=json_decode($get_tabla);
   //print_r ( $get_tabla );
   $reg=$get_tabla->registros;
   $get_pdf=$instancia_pdf->get_pdf($reg,json_decode(base64_decode( $data[1] )));
   echo $get_pdf;
});

// ######################################################################
$app->post('/ingreso_formulario/', function () use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type', 'application/json');
   //echo $_POST;
   $data = base64_decode( $app->request->post('data') );
   $data = json_decode( $data );
   //print_r($data);
   $ingreso_formulario=$instancia_db->ingreso_formulario($data);
   //print_r ( $data );
   echo ($ingreso_formulario);
});

$app->post('/actualizar_formulario/', function () use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type', 'application/json');
   $data = base64_decode( $app->request->post('data') );
   $data = json_decode( $data );
   //print_r($data);
   $actualizar_formulario=$instancia_db->actualizar_formulario($data);
   echo $actualizar_formulario;
});

$app->get('/login/get_user/:token', function ($token) use ($app) {
  $app->response->headers->set('Content-Type', 'application/json');
    try {

                //echo 'Mediante cURL '.$app->request()->headers('AGENTE'); // AGENTE DE EL cURL     || 1
                //echo "<hr>";
          // (( ($app->request()->headers('TOKEN')) && ($app->request()->headers('TOKEN')=='45d38a') )  )
      //######################################
      try {
                $decoded = JWT::decode($token, KEY, array('HS256'));
               /* $token = array(
                "iss" => "http://localhost/_api_emc/login/", //http://localhost/api_ipsd/login/
                "us" => $decoded->us,
                "cot"=>$decoded->cot,
                "iat" => (time()-21600));// FECHA DE CREACION SU VENCIMIENTO SERA 1800 FECHA VENCIMIENTO
                $jwt = JWT::encode($token, KEY);*/
                //echo '{"correcto":true,"user":"'.$decoded->us.'"}';
                include_once 'consultas.class.php';
                $instancia_db = new DbModelo();
                $app->response->headers->set('Content-Type', 'application/json');
                $data_info=$instancia_db->get_info_user($decoded->us);

                echo $data_info;
    } catch (Exception $e) {
        echo $e.'{"correcto":false,"msg":"Token Invalidoo"}';
    }
      //######################################

    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(401);
    }
 });
 $app->post('/busqueda_modal/', function () use ($app) {
   include_once 'consultas.class.php';
   $instancia_db = new DbModelo();
   $app->response->headers->set('Content-Type', 'application/json');
   $data = base64_decode( $app->request->post('data') );
   $data = json_decode( $data );
   $param=$data[0];
   $db_config=json_decode(base64_decode($data[1]));
   //print_r($db_config);
   $db=$db_config->db;
   $data_obj=json_decode($instancia_db->desencriptar($db));
   //$data_obj=json_decode($db_config);
   $from=$data_obj[0];
   $select=$data_obj[1].','.$data_obj[2].','.$data_obj[5][0];
   /*if ($data_obj[3]=='LIKE') {
			$where_param=$data_obj[2]." LIKE('%$param%')";
		}*/
    if ($data_obj[4][0]) {
      $where_param=$data_obj[4][1][$data[2]]." LIKE('%$param%')";
      //echo $data_obj[4][1];
      //$and=str_replace("$", "'",$data_obj[4][1]);
      //$where_param.=' '.$and;
    }else{
      $where_param=$data_obj[2]." LIKE('%$param%')";
    }
    $_db = new mysqli('LOCALHOST', 'sitrauna_user', '-kD3S6kq0cNv', 'sitrauna_db');
    $_db->set_charset("utf8");
    $str='SELECT '.$select.' FROM '.$from.' WHERE '.$where_param;
    //echo $str;
    // CONSULTAR A LA BASE DE DATOS
    $r = $_db->query($str);
    if ($r->num_rows) {
      $string_tabla='<thead><tr style="background: #eceff1;"><th style="width:20px;">#</th>';
      $campos_titulos='';
      $i3=0;
      while ($i3 < count($data_obj[5][1])) {
        $string_tabla.='<th style="width: '.$data_obj[5][1][$i3][1].'%;">'.$data_obj[5][1][$i3][0].'</th>';
        //echo '<td>'.$data_obj[5][1][$i3].'</td>';
        $i3++;
      }
      $string_tabla.='</tr></thead>';
      //print_r($r);
      $cadena_json='[';
      while ($fila = $r->fetch_row()) {
        $row='[';
          //printf ("%s (%s)\n", $fila[0], $fila[1]);
          //print_r($fila);
          $i=2;
          while ($i < $r->field_count) {
               $row.='"'.$fila[$i].'",';
            $i++;
          }
          $row = trim($row, ',').']';
				  $cadena_json.=$row.',';
      }
      $cadena_json = trim($cadena_json, ',').']';
      //$string_tabla.='</tr></tbody></table>';
      //echo $cadena_json;
      //echo $string_tabla;
      $string_tabla = str_replace('"', '\"', $string_tabla);
      echo '{"correcto":true,"registros":'.$_db->affected_rows.',"datos":'.$cadena_json.',"thead":"'.$string_tabla.'"}';
      /*if (count($data_obj[6])>1) {
    		for($i = 0, $l = count($data_obj[5]); $i < $l; ++$i) {
          $campos.=$data_obj[6][$i][0].', ';
    	 	  $campos_titulos.='"'.$data_obj[6][$i][1].'",';
    	 	//array_push($campos_arr, $arr->db[2][$i][0]);
        //echo $data_obj[6][$i][1];
    	}
    }*/
    	//print_r($campos_arr);
    	//$campos.="'a'";
      //$campos= trim($campos, ' ,');
      //$campos_titulos= '['.trim($campos_titulos, ',').']';
    	//$str="SELECT $campos FROM $tabla WHERE 1 AND $where_param";

    }else{
      echo '{"correcto":true,"registros":0}';
    }
    //$datos = $r->fetch_assoc();

 });
 $app->post('/busqueda_input/', function () use ($app) {
    include_once 'consultas.class.php';
    $instancia_db = new DbModelo();
    $app->response->headers->set('Content-Type', 'application/json');
    //echo $_POST;
    $data = base64_decode( $app->request->post('data') );
    $data = json_decode( $data );

    $array_string=$instancia_db->desencriptar($data[1]);
    //$query="SELECT ";
    $value=$data[0]; // VALOR DE LA CAJA DE TEXTO
    //echo $array_string;
    $data_obj=json_decode($array_string);
    // OBJETO PADRE
    //print_r($data_obj);
    //print_r($data);
    //echo $data_obj[1][0];
    // // if ($data_obj[1][0]) {
     $_db = new mysqli('LOCALHOST', 'sitrauna_user', '-kD3S6kq0cNv', 'sitrauna_db');
     $_db->set_charset("utf8");
     //$datos['r'];

     // PRIMERO SABER SI EXISTE REGISTRO CON ESE PARAMETRO
     $tabla=$data_obj[2][0];
     $campo=$data_obj[2][1];
     $where=$data_obj[2][2];
     $select=$data_obj[2][3];
     $str="SELECT $campo AS r FROM $tabla WHERE $where='$value';";
     //echo $str;
     $str_tabla=$select." WHERE $where='$value' LIMIT 1;";
     //echo $str;
     $r = $_db->query($str);
     $datos = $r->fetch_assoc();
     $param=$datos['r'];//echo $param;
     // EVALUAR CAMPOS DE RELLENO
     $str_campos_relleno='';
     $i=0;
     while ($i < count($data_obj[3][1])) { // CAMPOS DE RELLENO
       $str_campos_relleno.='["'.$data_obj[3][1][$i][1].'",""],';
       //echo $str_campos_relleno;
       //$validaciones[ $tablas[$i][0] ]='';
       $i++;
     }
     $str_campos_relleno= '['.trim($str_campos_relleno, ',').']';
     if ($r->num_rows) {
       // SIGNIFICA QUE ESE REGISTRO EXISTE
       // SI EXISTE ENTONCES EVALUAR EL WHERE PARA ESE REGISTRO
       $where_campo=1;
       if ($data_obj[1][0]) {
         // VALIDAR EL WHERE DE ESA MISMA TABLA
         // REEMPLAZAR $ POR '
         $q=str_replace("$", "'", $data_obj[1][1]);
         // REEMPLAZAR ? POR EL VALOR DE LA CAJA DE TEXTO
         $str=str_replace("?",$param,$q);
         //echo $str;
         $r = $_db->query($str);
         $datos = $r->fetch_assoc();
         //echo $datos['r'];
         // HACER UN QUERY DE NEGACION
         $where_campo=$datos['r'];
       }
       //echo $where_campo;
       if ($where_campo) {
         $string_tabla='<br><table class="tabla table table-striped table-bordered"><tbody style="font-size: 14px;">';
         $r = $_db->query($str_tabla);
         //print_r($r);
          // QUERY PARA TRAER LA BUSQUEDA EN LA ALERTA
         while ($fila = $r->fetch_row()) {
             //printf ("%s (%s)\n", $fila[0], $fila[1]);
             $i=0;
             $string_tabla.='<tr>';
             while ($i < $r->field_count) {
               $string_tabla.=('<td>'.$fila[$i].'</td>');
               //echo '<td>'.$fila[$i].'</td>';
               $i++;
             }
         }
         $string_tabla.='</tr></tbody></table>';
         //echo $string_tabla;
         $string_tabla = str_replace('"', '\"', $string_tabla);
         // RELLENAR LOS CAMPOS ASOCIADOS
         if ($data_obj[3][0]) {
             //print_r($data_obj[4][1][0][0]);
             $str_campos_relleno='';
             //print_r($data_obj[2]);
             $i=0;
             while ($i < count($data_obj[3][1])) { // CANTIDAD DE CONSULAS A REALIZAR (TABLAS) INICIALIZAR KEYS
               //echo "string";
               $str= "SELECT ".$data_obj[3][1][$i][0]." AS r FROM ".$data_obj[2][0]." WHERE $where='$value';";
               //echo $str;
               $r = $_db->query($str);
               $datos = $r->fetch_assoc();
               //echo $datos['r'];
               $str_campos_relleno.='["'.$data_obj[3][1][$i][1].'","'.$datos['r'].'"],';
               //echo $str_campos_relleno;
               //$validaciones[ $tablas[$i][0] ]='';
               $i++;
             }
             $str_campos_relleno= '['.trim($str_campos_relleno, ',').']';
             //echo $str_campos_relleno;
             //echo $data_obj[1];
             //$
         }
         echo '{"correcto":true,"id":"'.$data[2].'","msg":"'.$data[3].' ('.$value.') encontrado.","param":"'.$param.'","rellenar_campos":[true,'.$str_campos_relleno.'],"tabla_alert":["'.$data[2].'","'.$string_tabla.'"]}';
       }else{
         echo '{"correcto":false,"rellenar_campos":[true,'.$str_campos_relleno.'],"msg":"'.$data_obj[1][2].'","ids_error":[true,["'.$data[2].'"]]}';
       }

     }else{
       echo '{"correcto":false,"rellenar_campos":[true,'.$str_campos_relleno.'],"msg":"'.$data_obj[2][4].'","ids_error":[true,["'.$data[2].'"]]}';
     }
 });

$app->run();
