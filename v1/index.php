<?php

use Slim\Http\Request;
use Slim\Http\Response;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
require '../Slim/Slim.php';
require_once '../JWT.php';
require_once '../BeforeValidException.php';
require_once '../ExpiredException.php';
require_once '../SignatureInvalidException.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/login/:json', function ($json) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $response = new Response();
        $app->response->headers->set('Content-Type', 'application/json');
        $dataJson = json_decode(base64_decode($json));
        $data = $instancia_db->get_us_valido($dataJson[0], $dataJson[1]);
        echo '{"code":1,"description":"success","login":' . $data . '}';
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->get('/backup', function () use ($app) {
    require_once('./conn.php');
    $headers = "";
    $headers .= "Pragma: no-cache" . "\r\n";
    $headers .= "Expires: 0" . "\r\n";
    $headers .= 'Content-Transfer-Encoding: binary' . "\r\n";
    $date = date("Y-m-d");
    $lugar = $_SERVER['SCRIPT_FILENAME'];
    $fecha = date('Ymd_hi');
    $bd = "apoautis";
    $filename = $bd . "_" . $fecha . "_backup.sql";
    $usuario = "root";
    $passwd = "";
    $executa = "C:/xampp/mysql/bin/mysqldump.exe --skip-lock-tables --user=$usuario --password=$passwd -R --opt $bd > C:/xampp/htdocs/ApoautisApis/backup/$filename";
    system($executa, $resultado);

    if ($resultado) {
        echo json_encode("error");
    } else {
        echo json_encode("Listo");
    }
});


$app->get('/restor/:filename', function ($filename) use ($app) {
    require_once('./conn.php');
    $headers = "";
    $headers .= "Pragma: no-cache" . "\r\n";
    $headers .= "Expires: 0" . "\r\n";
    $headers .= 'Content-Transfer-Encoding: binary' . "\r\n";
    $bd = "apoautis";
    $path = 'C:/xampp/htdocs/ApoautisApis/backup/' . $filename;
    $usuario = "root";
    $passwd = "";
    $executa = "C:/xampp/mysql/bin/mysql.exe --user=$usuario --password=$passwd $bd < $path";
    system($executa, $resultado);

    if ($resultado) {
        echo json_encode("error");
    } else {
        echo json_encode("Listo");
    }
});



$app->get('/existeUsuario/:user', function ($user) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        include_once 'response.class.php';
        $instancia_db = new DbModelo();
        $response = new Response();
        $response->setCode(1);
        $response->setDescripcion("Success");
        $app->response->headers->set('Content-Type', 'application/json');
        $data_info = $instancia_db->getExisteUsuario($user);
        $response->setExisteUsuario($data_info);
        print_r(json_encode($response));
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->get('/correo/:user', function ($user) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        include_once 'response.class.php';
        $instancia_db = new DbModelo();
        $response = new Response();
        $app->response->headers->set('Content-Type', 'application/json');
        $data_info = $instancia_db->getInfoCorreo($user);
        $correo = $instancia_db->getEmailUser($user);
        $to = $correo;
        $subject = "¡Recuperación acceso!";
        $headers = "";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:eocr2397@hotmail.com' . "\r\n";
        $message = $data_info;
        if (mail($to, $subject, $message, $headers)) {
            echo '{"correcto":true}';
        } else {
            echo '{"correcto":false}';
        }
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->get('/comprobarToken/:token', function ($token) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        include_once 'response.class.php';
        $instancia_db = new DbModelo();
        $response = new Response();
        $response->setCode(1);
        $response->setDescripcion("Success");
        $app->response->headers->set('Content-Type', 'application/json');
        $data_info = $instancia_db->comprobarToken($token);
        echo $data_info;
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->get('/loginPreguntas/:json', function ($json) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        include_once 'response.class.php';
        $instancia_db = new DbModelo();
        $response = new Response();
        $response->setCode(1);
        $response->setDescripcion("Success");
        $app->response->headers->set('Content-Type', 'application/json');
        $dataJson = json_decode(base64_decode($json));
        $data = $instancia_db->getPreguntas($dataJson[0], $dataJson[1]);
        $response->setPreguntas($data);
        print_r(json_encode($response));
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});



$app->get('/comprobarPregunta/:json', function ($json) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        include_once 'response.class.php';
        $instancia_db = new DbModelo();
        $response = new Response();
        $dataJson = json_decode(base64_decode($json));
        $response->setCode(1);
        $response->setDescripcion("Success");
        $app->response->headers->set('Content-Type', 'application/json');
        $data = $instancia_db->comprobarPregunta($dataJson[2], $dataJson[0], $dataJson[1]);
        $response->setPreguntaSeguridad($data);
        print_r(json_encode($response));
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->get('/establecerPass/:json', function ($json) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    include_once 'response.class.php';
    $response = new Response();
    try {
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $dataJson = json_decode(base64_decode($json));
        $response->setCode(1);
        $response->setDescripcion("Success");
        $app->response->headers->set('Content-Type', 'application/json');
        if (count($dataJson) == 2) {
            $data = $instancia_db->insertPass($dataJson[0], $dataJson[1], 0);
        } else {
            $data = $instancia_db->insertPass($dataJson[0], $dataJson[1], $dataJson[2]);
        }
        $response->setPassEstablecido($data);
        print_r(json_encode($response));
    } catch (Exception $e) {
        $response->setCode(0);
        $response->setDescripcion($e->getMessage());
        $app->response->setStatus(500);
        print_r(json_encode($response));
    }
});

$app->get('/establecerPassPv/:json', function ($json) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    include_once 'response.class.php';
    $response = new Response();
    try {
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $dataJson = json_decode(base64_decode($json));
        $response->setCode(1);
        $response->setDescripcion("Success");
        $app->response->headers->set('Content-Type', 'application/json');
        $data = $instancia_db->insertPassPv($dataJson[0], $dataJson[1], $dataJson[2], $dataJson[3]);
        $response->setPassEstablecido($data);
        print_r(json_encode($response));
    } catch (Exception $e) {
        $response->setCode(0);
        $response->setDescripcion($e->getMessage());
        $app->response->setStatus(500);
        print_r(json_encode($response));
    }
});

$app->get('/getTabla/:id', function ($id) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $get_tabla = $instancia_db->get_tabla($id);
        //print_r($data);
        print_r($get_tabla);
        //echo $response->getCode();
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->options('/insertData', function (Request $request, Response $response) use ($app) {
    return $response;
});

$app->get('/insertData/:data', function ($data) use ($app) {
    include_once 'consultas.class.php';
    $instancia_db = new DbModelo();
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    $dataDecod = (json_decode(base64_decode($data)));
    $set_tabla = $instancia_db->setTable($dataDecod);
    echo $set_tabla;
});

$app->get('/editData/:data', function ($data) use ($app) {
    include_once 'consultas.class.php';
    $instancia_db = new DbModelo();
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    $dataDecod = (json_decode(base64_decode($data)));
    $set_tabla = $instancia_db->editData($dataDecod);
    echo $set_tabla;
});

$app->get('/dataRow/:data', function ($data) use ($app) {
    include_once 'consultas.class.php';
    $instancia_db = new DbModelo();
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    $dataDecod = (json_decode(base64_decode($data)));
    $set_tabla = $instancia_db->dataRow($dataDecod);
    echo $set_tabla;
});

$app->get('/configForm/:id', function ($data) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $dataDecod = (json_decode(base64_decode($data)));
        $get_config = $instancia_db->configForm($dataDecod);
        print_r($get_config);
        //echo $response->getCode();
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});

$app->get('/getTabla2/:id', function ($id) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    try {
        include_once 'consultas.class.php';
        $instancia_db = new DbModelo();
        $get_tabla = $instancia_db->get_tabla2($id);
        //print_r($data);
        print_r($get_tabla);
        //echo $response->getCode();
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $app->response->setStatus(500);
    }
});


$app->get('/nivel_academico', function () {
    require_once('./conn.php');
    $query = "select * from tbl_nivel_academico";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    $respuesta = [
        'error' => true,
        'mensaje' => 'dd'
    ];
    echo json_encode($data);
});


$app->post('/nivel_academico', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Nivel_academico = $data->Nivel_academico;
        $query = "INSERT INTO tbl_nivel_academico (Nivel_academico) VALUES(upper('$Nivel_academico'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $Nivel_academico = $data->Nivel_academico;
        $cod = $data->Cod_nivel_academico;
        $query = "UPDATE tbl_nivel_academico SET Nivel_academico='$Nivel_academico' WHERE Cod_nivel_academico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_nivel_academico;
        $query = "DELETE FROM tbl_nivel_academico WHERE Cod_nivel_academico=$cod";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/nacionalidad', function () {
    require_once('./conn.php');
    $query = "select * from tbl_nacionalidad";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/nacionalidad', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_nacionalidad = $data->Cod_nacionalidad;
        $nacionalidad = $data->Nacionalidad;
        $query = "INSERT INTO tbl_nacionalidad (Cod_nacionalidad, Nacionalidad) VALUES('$Cod_nacionalidad','$nacionalidad')";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_nacionalidad;
        $nacionalidad = $data->Nacionalidad;
        $query = "UPDATE tbl_nacionalidad SET Cod_nacionalidad='$cod', Nacionalidad='$nacionalidad' WHERE Cod_nacionalidad='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_nacionalidad;
        $query = "DELETE FROM tbl_nacionalidad WHERE Cod_nacionalidad='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/sedes', function () {
    require_once('./conn.php');
    $query = "select * from tbl_sede";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/sedes', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_nacionalidad = $data->Cod_nacionalidad;
        $nacionalidad = $data->Nacionalidad;

        $query = "INSERT INTO apoautis.tbl_sede
        (Cod_departamento, Cod_estatus, Nombre_sede, Direccion_sede, Telefono_sede, Correo_electronico_sede, Redes_sociales, Administrador_general, Usuario_registro, Fecha_registro, Usuario_modificacion, Fecha_modificacion)
        VALUES(0, 1, '', '', '', '', NULL, NULL, '', '', NULL, NULL);
        ";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_nacionalidad;
        $nacionalidad = $data->Nacionalidad;
        $query = "UPDATE tbl_nacionalidad SET Nacionalidad='$nacionalidad' WHERE Cod_nacionalidad='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_nacionalidad;
        $query = "DELETE FROM tbl_nacionalidad WHERE Cod_nacionalidad='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/tipo_persona', function () {
    require_once('./conn.php');
    $query = "select * from tbl_tipo_persona";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/tipo_persona', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Tipo_persona = $data->Tipo_persona;
        $query = "INSERT INTO tbl_tipo_persona(Tipo_persona)VALUES(upper('$Tipo_persona'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_tipo_persona;
        $Tipo_persona = $data->Tipo_persona;
        $query = "UPDATE tbl_tipo_persona SET Tipo_persona=upper('$Tipo_persona') WHERE Cod_tipo_persona='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_tipo_persona;
        $query = "DELETE FROM tbl_tipo_persona WHERE Cod_tipo_persona='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/tipo_evaluacion', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_tipo_evaluacion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/tipo_evaluacion', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Tipo_evaluacion = $data->Tipo_evaluacion;
        $query = "INSERT INTO tbl_tipo_evaluacion (Tipo_evaluacion) VALUES(upper('$Tipo_evaluacion'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_tipo_evaluacion;
        $Tipo_evaluacion = $data->Tipo_evaluacion;
        $query = "UPDATE tbl_tipo_evaluacion SET Tipo_evaluacion=upper('$Tipo_evaluacion') WHERE Cod_tipo_evaluacion='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_tipo_evaluacion;
        $query = "DELETE FROM tbl_tipo_evaluacion WHERE Cod_tipo_evaluacion='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/genero', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_genero";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/genero', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $genero = $data->Genero;
        $query = "INSERT INTO tbl_genero(Genero) VALUES(upper('$genero'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_genero;
        $genero = $data->Genero;
        $query = "UPDATE tbl_genero SET Genero=upper('$genero') WHERE Cod_genero='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_genero;
        $query = "DELETE FROM tbl_genero WHERE Cod_genero='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/parentesco', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_parentesco";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/parentesco', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $parentesco = $data->Parentesco;
        $query = "INSERT INTO tbl_parentesco (Parentesco) VALUES(upper('$parentesco'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_parentesco;
        $parentesco = $data->Parentesco;
        $query = "UPDATE tbl_parentesco SET Parentesco=upper('$parentesco') WHERE Cod_parentesco='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_parentesco;
        $query = "DELETE FROM tbl_parentesco WHERE Cod_parentesco='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/modalidad_servicio', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_modalidad_servicio";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/modalidad_servicio', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $modalidad = $data->Modalidad_servicio;
        $query = "INSERT INTO tbl_modalidad_servicio (Modalidad_servicio) VALUES(upper('$modalidad'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_modalidad_servicio;
        $parentesco = $data->Modalidad_servicio;
        $query = "UPDATE tbl_modalidad_servicio SET Modalidad_servicio=upper('$parentesco') WHERE Cod_modalidad_servicio='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_modalidad_servicio;
        $query = "DELETE FROM tbl_modalidad_servicio WHERE Cod_modalidad_servicio='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/modalidad_atencion', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_modalidad_atencion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/modalidad_atencion', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $modalidad = $data->Modalidad_atencion;
        $query = "INSERT INTO tbl_modalidad_atencion (Modalidad_atencion) VALUES(upper('$modalidad'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_modalidad_atencion;
        $parentesco = $data->Modalidad_atencion;
        $query = "UPDATE tbl_modalidad_atencion SET Modalidad_atencion='$parentesco' WHERE Cod_modalidad_atencion='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_modalidad_atencion;
        $query = "DELETE FROM tbl_modalidad_atencion WHERE Cod_modalidad_atencion='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/tipo_especialidad', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_tipo_especialidad";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/tipo_especialidad', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Tipo_especialidad = $data->Tipo_especialidad;
        $descripcion = $data->Descripcion_especialidad;
        $usuario_registro = 'Admin';
        $query = "INSERT INTO tbl_tipo_especialidad
        (Tipo_especialidad, Descripcion_especialidad,Usuario_registro, Fecha_registro)
        VALUES(upper('$Tipo_especialidad'),upper('$descripcion'),'$usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_tipo_especialidad;
        $Tipo_especialidad = $data->Tipo_especialidad;
        $descripcion = $data->Descripcion_especialidad;
        $usuario_registro = 'Admin';
        $query = "UPDATE tbl_tipo_especialidad SET Tipo_especialidad=upper('$Tipo_especialidad'), Descripcion_especialidad=upper('$descripcion'),Usuario_modificacion='$usuario_registro', Fecha_modificacion=now()
        WHERE Cod_tipo_especialidad='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_tipo_especialidad;
        $query = "DELETE FROM tbl_tipo_especialidad
        WHERE Cod_tipo_especialidad='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/tipo_inclusion', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_tipo_inclusion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/tipo_inclusion', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Tipo_inclusion = $data->Tipo_inclusion;
        $query = "INSERT INTO tbl_tipo_inclusion (Tipo_inclusion) VALUES(upper('$Tipo_inclusion'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_tipo_inclusion;
        $Tipo_inclusion = $data->Tipo_inclusion;
        $query = "UPDATE tbl_tipo_inclusion
        SET Tipo_inclusion=upper('$Tipo_inclusion')
        WHERE Cod_tipo_inclusion='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_tipo_inclusion;
        $query = "DELETE FROM tbl_tipo_inclusion
        WHERE Cod_tipo_inclusion='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/status', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_estatus";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/status', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Estatus = $data->Estatus;
        $query = "INSERT INTO tbl_estatus(Estatus) VALUES(upper('$Estatus'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_estatus;
        $Estatus = $data->Estatus;
        $query = "UPDATE tbl_estatus
        SET Estatus=upper('$Estatus')
        WHERE Cod_estatus='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_estatus;
        $query = "DELETE FROM tbl_estatus
        WHERE Cod_estatus='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/parametros', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_parametro";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/parametros', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Parametro = $data->Parametro;
        $Valor = $data->Valor;
        $query = "INSERT INTO tbl_parametro
        (Parametro, Valor, Fecha_registro)
        VALUES('$Parametro',$Valor,now())";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_parametro;
        $Parametro = $data->Parametro;
        $Valor = $data->Valor;
        $query = "UPDATE tbl_parametro
        SET Parametro='$Parametro',Valor='$Valor',Fecha_modificacion=now()
        WHERE Cod_parametro='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_parametro;
        $query = "DELETE FROM tbl_parametro
        WHERE Cod_parametro='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/departamento_laboral', function () {
    require_once('./conn.php');
    $query = "SELECT * FROM tbl_departamento_laboral";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/departamento_laboral', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Departamento_laboral = $data->Departamento_laboral;
        $query = "INSERT INTO tbl_departamento_laboral
        (Departamento_laboral)
        VALUES('$Departamento_laboral')";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_departamento_laboral;
        $Departamento_laboral = $data->Departamento_laboral;
        $query = "UPDATE tbl_departamento_laboral
        SET Departamento_laboral='$Departamento_laboral'
        WHERE Cod_departamento_laboral='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_departamento_laboral;
        $query = "DELETE FROM tbl_departamento_laboral
        WHERE Cod_departamento_laboral='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/colaborador', function () {
    require_once('./conn.php');
    $query = "select * from tbl_colaborador tc inner join
    tbl_persona tp on tc.Cod_persona = tp.Cod_persona
    inner join tbl_sede ts on tc.Cod_sede = ts.Cod_sede
    inner join tbl_departamento_laboral tdl on tc.Cod_departamento_laboral = tdl.Cod_departamento_laboral";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/colaborador', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;


    if ($tipo == "post") {
        $Cod_tipo_persona = $data->Cod_tipo_persona;
        $Cod_genero = $data->Cod_genero;
        $Cod_nacionalidad = $data->Cod_nacionalidad;
        $Cod_estado_civil = $data->Cod_estado_civil;
        $Cod_departamento = $data->Cod_departamento;
        $Cod_estatus = $data->Cod_estatus;
        $Nombre = $data->Nombre;
        $Apellido = $data->Apellido;
        $No_identidad = $data->No_identidad;
        $Documento_id = $data->Documento_id;
        $Lugar_nacimiento = $data->Lugar_nacimiento;
        $Fecha_nacimiento = $data->Fecha_nacimiento;
        $Residencia_actual = $data->Residencia_actual;
        $Telefono_fijo = $data->Telefono_fijo;
        $Correo_electronico = $data->Correo_electronico;
        $Telefono_celular = $data->Telefono_celular;
        $Usuario_registro = $data->Usuario_registro;
        ///////////////
        $Cod_sede = $data->Cod_sede;
        $cod_departamento_laboral = $data->Cod_departamento_laboral;
        $Cargo_principal = $data->Cargo_principal;
        $Descripcion_funciones = $data->Descripcion_funciones;
        $Fecha_contratacion = $data->Fecha_contratacion;
        $Usuario_registro = $data->Usuario_registro;
        $query = "call colaborador($Cod_sede,$cod_departamento_laboral,'$Cargo_principal','$Descripcion_funciones','$Fecha_contratacion',$Cod_tipo_persona,$Cod_genero,'$Cod_nacionalidad',$Cod_estado_civil,$Cod_departamento,$Cod_estatus,'$Nombre','$Apellido','$No_identidad','$Documento_id','$Lugar_nacimiento','$Fecha_nacimiento','$Residencia_actual',$Telefono_fijo,'$Correo_electronico',$Telefono_celular,'$Usuario_registro')";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);

        $respuesta = [
            'error' => $result,
            'mensaje' => $error,

        ];
    }


    if ($tipo == "update") {
        $Cod_tipo_persona = $data->Cod_tipo_persona;
        $Cod_genero = $data->Cod_genero;
        $Cod_nacionalidad = $data->Cod_nacionalidad;
        $Cod_estado_civil = $data->Cod_estado_civil;
        $Cod_departamento = $data->Cod_departamento;
        $Cod_estatus = $data->Cod_estatus;
        $Nombre = $data->Nombre;
        $Apellido = $data->Apellido;
        $No_identidad = $data->No_identidad;
        $Documento_id = $data->Documento_id;
        $Lugar_nacimiento = $data->Lugar_nacimiento;
        $Fecha_nacimiento = $data->Fecha_nacimiento;
        $Residencia_actual = $data->Residencia_actual;
        $Telefono_fijo = $data->Telefono_fijo;
        $Correo_electronico = $data->Correo_electronico;
        $Telefono_celular = $data->Telefono_celular;
        $Usuario_registro = $data->Usuario_registro;
        ///////////////
        $Cod_sede = $data->Cod_sede;
        $cod_departamento_laboral = $data->Cod_departamento_laboral;
        $Cargo_principal = $data->Cargo_principal;
        $Descripcion_funciones = $data->Descripcion_funciones;
        $Fecha_contratacion = $data->Fecha_contratacion;
        $Usuario_registro = $data->Usuario_registro;
        $query = "call colaboradoredit($Cod_tipo_persona,$Cod_sede,$cod_departamento_laboral,'$Cargo_principal','$Descripcion_funciones','$Fecha_contratacion',$Cod_tipo_persona,$Cod_genero,'$Cod_nacionalidad',$Cod_estado_civil,$Cod_departamento,$Cod_estatus,'$Nombre','$Apellido','$No_identidad','$Documento_id','$Lugar_nacimiento','$Fecha_nacimiento','$Residencia_actual',$Telefono_fijo,'$Correo_electronico',$Telefono_celular,'$Usuario_registro')";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_colaborador;
        $query = "DELETE FROM tbl_colaborador
        WHERE Cod_colaborador='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/persona', function () {
    require_once('./conn.php');
    $query = "select *
    FROM tbl_persona p inner join tbl_tipo_persona ttp on p.Cod_tipo_persona = ttp.Cod_tipo_persona
    inner join tbl_sede ts on p.Cod_sede = ts.Cod_sede 
    inner join tbl_genero tg on p.Cod_genero = tg.Cod_genero 
    inner join tbl_nacionalidad tn on p.Cod_nacionalidad = tn.Cod_nacionalidad 
    inner join tbl_estado_civil tec on p.Cod_estado_civil = tec.Cod_estado_civil 
    inner join tbl_departamento td on p.Cod_departamento = td.Cod_departamento 
    inner join tbl_ficha_general tfg on p.Cod_persona = tfg.Cod_persona 
    inner join tbl_ficha_inclusion tfi on p.Cod_persona = tfi.Cod_persona
    inner join tbl_tipo_inclusion tti on tfi.Cod_tipo_inclusion = tti.Cod_tipo_inclusion 
    inner join tbl_tipo_institucion tti2 on tfi.Cod_tipo_institucion = tti2.Cod_tipo_institucion 
    inner join tbl_ficha_salud tfs on p.Cod_persona = tfs.Cod_persona 
    inner join tbl_familiar_encargado tfe on p.Cod_persona = tfe.Cod_persona
    inner join tbl_parentesco tp on tfe.Cod_parentesco = tp.Cod_parentesco ";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/persona', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_sede = $data->Cod_sede;
        $Cod_tipo_persona = $data->Cod_tipo_persona;
        $Cod_genero = $data->Cod_genero;
        $Cod_nacionalidad = $data->Cod_nacionalidad;
        $Cod_estado_civil = $data->Cod_estado_civil;
        $Cod_departamento = $data->Cod_departamento;
        $Cod_estatus = $data->Cod_estatus;
        $Nombre = $data->Nombre;
        $Apellido = $data->Apellido;
        $No_identidad = $data->No_identidad;
        $Documento_id = $data->Documento_id;
        $Lugar_nacimiento = $data->Lugar_nacimiento;
        $Fecha_nacimiento = $data->Fecha_nacimiento;
        $Residencia_actual = $data->Residencia_actual;
        $Telefono_fijo = $data->Telefono_fijo;
        $Correo_electronico = $data->Correo_electronico;
        $Telefono_celular = $data->Telefono_celular;
        $Usuario_registro = $data->Usuario_registro;
        /////////////////////////////////////////////
        $Cod_ficha_general = $data->Cod_ficha_general;
        $Carnet_discapacidad = $data->Carnet_discapacidad;
        $Acceso_computadora = $data->Acceso_computadora;
        $Acceso_internet = $data->Acceso_internet;
        $Bono_discapacidad = $data->Bono_discapacidad;
        $Instituto_procedencia = $data->Instituto_procedencia;
        $Permanencia_institucion = $data->Permanencia_institucion;
        $Nivel_academico = $data->Nivel_academico;
        $Telefono_instituto = $data->Telefono_instituto;
        $Correo_instituto = $data->Correo_instituto;
        /////////////////////////////////////////////////
        $Cod_ficha_inclusion = $data->Cod_ficha_inclusion;
        $Cod_tipo_inclusion = $data->Cod_tipo_inclusion;
        $Cod_tipo_institucion = $data->Cod_tipo_institucion;
        $Nombre_institucion_empresa = $data->Nombre_institucion_empresa;
        $Direccion_institucion = $data->Direccion_institucion;
        $Telefono_institucion = $data->Telefono_institucion;
        $Correo_electronico_institucion = $data->Correo_electronico_institucion;
        $Fecha_ingreso = $data->Fecha_ingreso;
        $Grado_academico = $data->Grado_academico;
        $Seguimiento_inclusivo = $data->Seguimiento_inclusivo;
        $Tiempo_seguimiento_inclusivo = $data->Tiempo_seguimiento_inclusivo;
        $Cargo_desempeñar = $data->Cargo_desempeñar;
        $Fecha_matricula = $data->Fecha_matricula;
        ////////////////////////////////////////////////////
        $Cod_estado_nutricional = $data->Cod_estado_nutricional;
        $Agudeza_visual = $data->Agudeza_visual;
        $Agudeza_auditiva = $data->Agudeza_auditiva;
        $Condicion_oral_deficiente = $data->Condicion_oral_deficiente;
        $Esquema_vacunacion = $data->Esquema_vacunacion;
        $Anemia = $data->Anemia;
        $Alergia = $data->Alergia;
        $Descripcion_alergias = $data->Descripcion_alergias;
        $Enfermedad_cronica = $data->Enfermedad_cronica;
        $Descripcion_enf_cronica = $data->Descripcion_enf_cronica;
        $Seguimiento_medico = $data->Seguimiento_medico;
        $Medicacion = $data->Medicacion;
        $Nombre_medicamento = $data->Nombre_medicamento;
        $Dosis = $data->Dosis;
        $Tiempo_ingesta_medicacion = $data->Tiempo_ingesta_medicacion;
        $Seguimiento_terapia_alternativa = $data->Seguimiento_terapia_alternativa;
        $Tipo_terapia_alternativa = $data->Tipo_terapia_alternativa;
        //////////////////////////////////////////////////////////////////////
        $Cod_parentesco = $data->Cod_parentesco;
        $Cod_nivel_academico = $data->Cod_nivel_academico;
        $Nombre_familiar = $data->Nombre_familiar;
        $Identidad = $data->Identidad;
        $Telefono = $data->Telefono;
        $Correo = $data->Correo;
        $Ocupacion_actual = $data->Ocupacion_actual;
        $Labora_actualmente = $data->Labora_actualmente;
        $Enfermedades_cronicas = $data->Enfermedades_cronicas;
        $Descripcion_enfermedades_cronicas = $data->Descripcion_enfermedades_cronicas;
        $Ingreso_promedio_personal = $data->Ingreso_promedio_personal;
        $Miembros_familia = $data->Miembros_familia;
        $Ingreso_promedio_familiar = $data->Ingreso_promedio_familiar;
        $Monto_ingreso = $data->Monto_ingreso;
        $Ingreso_semanal = $data->Ingreso_semanal;
        $Ingreso_mensual = $data->Ingreso_mensual;




        $query = "call beneficiarios($Cod_sede,$Cod_tipo_persona,$Cod_genero,'$Cod_nacionalidad',$Cod_estado_civil,$Cod_departamento,$Cod_estatus,
                 '$Nombre','$Apellido','$No_identidad','$Documento_id','$Lugar_nacimiento','$Fecha_nacimiento','$Residencia_actual',$Telefono_fijo,'$Correo_electronico',
                 $Telefono_celular,'$Usuario_registro','$Carnet_discapacidad','$Acceso_computadora','$Acceso_internet','$Bono_discapacidad',
                 '$Instituto_procedencia','$Permanencia_institucion','$Nivel_academico','$Telefono_instituto','$Correo_instituto',
                 $Cod_tipo_inclusion,
                 $Cod_tipo_institucion,
                 '$Nombre_institucion_empresa',
                 '$Direccion_institucion',
                 '$Telefono_institucion',
                 '$Correo_electronico_institucion',
                 '$Fecha_ingreso',
                 '$Grado_academico',
                 '$Seguimiento_inclusivo',
                 '$Tiempo_seguimiento_inclusivo',
                 '$Cargo_desempeñar',
                 '$Fecha_matricula',
                 $Cod_estado_nutricional,
                 '$Agudeza_visual',
                 '$Agudeza_auditiva',
                 '$Condicion_oral_deficiente',
                 '$Esquema_vacunacion',
                 '$Anemia',
                 '$Alergia',
                 '$Descripcion_alergias',
                 '$Enfermedad_cronica',
                 '$Descripcion_enf_cronica',
                 '$Seguimiento_medico',
                 '$Medicacion',
                 '$Nombre_medicamento',
                 '$Dosis',
                 '$Tiempo_ingesta_medicacion',
                 '$Seguimiento_terapia_alternativa',
                 '$Tipo_terapia_alternativa',
                 $Cod_parentesco,
                 1180,
                 '$Ocupacion_actual',
                  '$Labora_actualmente',
                  '$Enfermedades_cronicas',
                    '$Descripcion_enfermedades_cronicas',
                 $Ingreso_promedio_personal,
                  '$Miembros_familia',
                   $Ingreso_promedio_familiar,
                    $Monto_ingreso,
                     $Ingreso_semanal,
                 $Ingreso_mensual,
                 '$Nombre_familiar',
                 '$Identidad',
                 '$Telefono',
                 '$Correo')";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);

        $respuesta = [
            'error' => $result,
            'mensaje' => $error,

        ];
    }


    if ($tipo == "update") {

        $cod = $data->Cod_persona;
        $Cod_sede = $data->Cod_sede;
        $Cod_tipo_persona = $data->Cod_tipo_persona;
        $Cod_genero = $data->Cod_genero;
        $Cod_nacionalidad = $data->Cod_nacionalidad;
        $Cod_estado_civil = $data->Cod_estado_civil;
        $Cod_departamento = $data->Cod_departamento;
        $Cod_estatus = $data->Cod_estatus;
        $Nombre = $data->Nombre;
        $Apellido = $data->Apellido;
        $No_identidad = $data->No_identidad;
        $Documento_id = $data->Documento_id;
        $Lugar_nacimiento = $data->Lugar_nacimiento;
        $Fecha_nacimiento = $data->Fecha_nacimiento;
        $Residencia_actual = $data->Residencia_actual;
        $Telefono_fijo = $data->Telefono_fijo;
        $Correo_electronico = $data->Correo_electronico;
        $Telefono_celular = $data->Telefono_celular;
        $Usuario_registro = $data->Usuario_registro;
        /////////////////////////////////////////////
        $Cod_ficha_general = $data->Cod_ficha_general;
        $Carnet_discapacidad = $data->Carnet_discapacidad;
        $Acceso_computadora = $data->Acceso_computadora;
        $Acceso_internet = $data->Acceso_internet;
        $Bono_discapacidad = $data->Bono_discapacidad;
        $Instituto_procedencia = $data->Instituto_procedencia;
        $Permanencia_institucion = $data->Permanencia_institucion;
        $Nivel_academico = $data->Nivel_academico;
        $Telefono_instituto = $data->Telefono_instituto;
        $Correo_instituto = $data->Correo_instituto;
        /////////////////////////////////////////////////
        $Cod_ficha_inclusion = $data->Cod_ficha_inclusion;
        $Cod_tipo_inclusion = $data->Cod_tipo_inclusion;
        $Cod_tipo_institucion = $data->Cod_tipo_institucion;
        $Nombre_institucion_empresa = $data->Nombre_institucion_empresa;
        $Direccion_institucion = $data->Direccion_institucion;
        $Telefono_institucion = $data->Telefono_institucion;
        $Correo_electronico_institucion = $data->Correo_electronico_institucion;
        $Fecha_ingreso = $data->Fecha_ingreso;
        $Grado_academico = $data->Grado_academico;
        $Seguimiento_inclusivo = $data->Seguimiento_inclusivo;
        $Tiempo_seguimiento_inclusivo = $data->Tiempo_seguimiento_inclusivo;
        $Cargo_desempeñar = $data->Cargo_desempeñar;
        $Fecha_matricula = $data->Fecha_matricula;
        ////////////////////////////////////////////////////
        $Cod_estado_nutricional = $data->Cod_estado_nutricional;
        $Agudeza_visual = $data->Agudeza_visual;
        $Agudeza_auditiva = $data->Agudeza_auditiva;
        $Condicion_oral_deficiente = $data->Condicion_oral_deficiente;
        $Esquema_vacunacion = $data->Esquema_vacunacion;
        $Anemia = $data->Anemia;
        $Alergia = $data->Alergia;
        $Descripcion_alergias = $data->Descripcion_alergias;
        $Enfermedad_cronica = $data->Enfermedad_cronica;
        $Descripcion_enf_cronica = $data->Descripcion_enf_cronica;
        $Seguimiento_medico = $data->Seguimiento_medico;
        $Medicacion = $data->Medicacion;
        $Nombre_medicamento = $data->Nombre_medicamento;
        $Dosis = $data->Dosis;
        $Tiempo_ingesta_medicacion = $data->Tiempo_ingesta_medicacion;
        $Seguimiento_terapia_alternativa = $data->Seguimiento_terapia_alternativa;
        $Tipo_terapia_alternativa = $data->Tipo_terapia_alternativa;
        //////////////////////////////////////////////////////////////////////
        $Cod_parentesco = $data->Cod_parentesco;
        $Cod_nivel_academico = $data->Cod_nivel_academico;
        $Nombre_familiar = $data->Nombre_familiar;
        $Identidad = $data->Identidad;
        $Telefono = $data->Telefono;
        $Correo = $data->Correo;
        $Ocupacion_actual = $data->Ocupacion_actual;
        $Labora_actualmente = $data->Labora_actualmente;
        $Enfermedades_cronicas = $data->Enfermedades_cronicas;
        $Descripcion_enfermedades_cronicas = $data->Descripcion_enfermedades_cronicas;
        $Ingreso_promedio_personal = $data->Ingreso_promedio_personal;
        $Miembros_familia = $data->Miembros_familia;
        $Ingreso_promedio_familiar = $data->Ingreso_promedio_familiar;
        $Monto_ingreso = $data->Monto_ingreso;
        $Ingreso_semanal = $data->Ingreso_semanal;
        $Ingreso_mensual = $data->Ingreso_mensual;

        $query = "call editbeneficiarios($cod,$Cod_sede,$Cod_tipo_persona,$Cod_genero,'$Cod_nacionalidad',$Cod_estado_civil,$Cod_departamento,$Cod_estatus,
         '$Nombre','$Apellido','$No_identidad','$Documento_id','$Lugar_nacimiento','$Fecha_nacimiento','$Residencia_actual',$Telefono_fijo,'$Correo_electronico',
         $Telefono_celular,'$Usuario_registro','$Carnet_discapacidad','$Acceso_computadora','$Acceso_internet','$Bono_discapacidad',
         '$Instituto_procedencia','$Permanencia_institucion','$Nivel_academico','$Telefono_instituto','$Correo_instituto',
         $Cod_tipo_inclusion,
         $Cod_tipo_institucion,
         '$Nombre_institucion_empresa',
         '$Direccion_institucion',
         '$Telefono_institucion',
         '$Correo_electronico_institucion',
         '$Fecha_ingreso',
         '$Grado_academico',
         '$Seguimiento_inclusivo',
         '$Tiempo_seguimiento_inclusivo',
         '$Cargo_desempeñar',
         '$Fecha_matricula',
         $Cod_estado_nutricional,
         '$Agudeza_visual',
         '$Agudeza_auditiva',
         '$Condicion_oral_deficiente',
         '$Esquema_vacunacion',
         '$Anemia',
         '$Alergia',
         '$Descripcion_alergias',
         '$Enfermedad_cronica',
         '$Descripcion_enf_cronica',
         '$Seguimiento_medico',
         '$Medicacion',
         '$Nombre_medicamento',
         '$Dosis',
         '$Tiempo_ingesta_medicacion',
         '$Seguimiento_terapia_alternativa',
         '$Tipo_terapia_alternativa',
         $Cod_parentesco,
         1180,
         '$Ocupacion_actual',
          '$Labora_actualmente',
          '$Enfermedades_cronicas',
            '$Descripcion_enfermedades_cronicas',
         $Ingreso_promedio_personal,
          '$Miembros_familia',
           $Ingreso_promedio_familiar,
            $Monto_ingreso,
             $Ingreso_semanal,
         $Ingreso_mensual,
         '$Nombre_familiar',
         '$Identidad',
         '$Telefono',
         '$Correo')";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_diagnostico;
        $query = "DELETE FROM tbl_diagnostico_evaluacion
        WHERE Cod_diagnostico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/diagnostico_evaluacion', function () {
    require_once('./conn.php');
    $query = "SELECT tde.Cod_diagnostico,
    tde.Cod_persona,
    tde.Diagnostico_general,
    tde.Doc_informe_diagnostico,
    tde.Usuario_registro,
    tde.Fecha_registro,
    tde.Usuario_modificacion,
    tde.Fecha_modificacion,
    tp.Nombre 
    from tbl_diagnostico_evaluacion tde inner join tbl_persona tp on tde.Cod_persona = tp.Cod_persona";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});




$app->post('/diagnostico_evaluacion', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $codigo_persona = $data->Cod_persona;
        $diagnostico = $data->Diagnostico_general;
        $doc = $data->Doc_informe_diagnostico;
        $Usuario_registro = $data->Usuario_registro;
        $query = "INSERT INTO tbl_diagnostico_evaluacion
        (Cod_persona, Diagnostico_general, Doc_informe_diagnostico, Usuario_registro, Fecha_registro)
        VALUES($codigo_persona,upper('$diagnostico'),'$doc','$Usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_diagnostico;
        $codigo_persona = $data->Cod_persona;
        $diagnostico = $data->Diagnostico_general;
        $doc = $data->Doc_informe_diagnostico;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_diagnostico_evaluacion 
         SET Diagnostico_general='$diagnostico', Doc_informe_diagnostico='$doc',Usuario_modificacion='$Usuario_registro', Fecha_modificacion=now() WHERE Cod_diagnostico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_diagnostico;
        $query = "DELETE FROM tbl_diagnostico_evaluacion
        WHERE Cod_diagnostico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/permisos', function () {
    require_once('./conn.php');
    $query = "select * from tbl_permisos tp inner join tbl_rol tr on tp.Cod_rol = tr.Cod_rol inner join tbl_objetos to2 on tp.Cod_objeto = to2.Cod_objeto";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/permisos', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $cod = $data->Cod_permiso;
        $Cod_rol = $data->Cod_rol;
        $Cod_objeto = $data->Cod_objeto;
        $Permiso_insertar = $data->Permiso_insertar;
        $Permiso_eliminar = $data->Permiso_eliminar;
        $Permiso_actualizar = $data->Permiso_actualizar;
        $Permiso_consultar = $data->Permiso_consultar;
        $query = "INSERT INTO tbl_permisos
        (Cod_rol, Cod_objeto, Permiso_insertar, Permiso_eliminar, Permiso_actualizar, Permiso_consultar)
        VALUES($Cod_rol,$Cod_objeto,$Permiso_insertar,$Permiso_eliminar,$Permiso_actualizar,$Permiso_consultar)";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_permiso;
        $Cod_rol = $data->Cod_rol;
        $Cod_objeto = $data->Cod_objeto;
        $Permiso_insertar = $data->Permiso_insertar;
        $Permiso_eliminar = $data->Permiso_eliminar;
        $Permiso_actualizar = $data->Permiso_actualizar;
        $Permiso_consultar = $data->Permiso_consultar;
        $query = "UPDATE tbl_permisos SET Cod_rol=$Cod_rol,Cod_objeto=$Cod_objeto,Permiso_insertar=$Permiso_insertar,Permiso_eliminar=$Permiso_eliminar,
        Permiso_actualizar=$Permiso_actualizar,Permiso_consultar=$Permiso_consultar WHERE Cod_permiso='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_permiso;
        $query = "DELETE FROM tbl_permisos
        WHERE Cod_permiso='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/roles', function () {
    require_once('./conn.php');
    $query = "select * from tbl_rol tr inner join tbl_estatus te on tr.Cod_estatus = te.Cod_estatus ";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/roles', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $rol = $data->Rol;
        $Cod_estatus = $data->Cod_status;
        $Descripcion = $data->Descripcion;
        $Usuario_registro = $data->Usuario_registro;
        $query = "INSERT INTO tbl_rol
        (Rol, Cod_estatus, Descripcion, Usuario_registro, Fecha_registro)
        VALUES(upper('$rol'),$Cod_estatus,upper('$Descripcion'),'$Usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_rol;
        $rol = $data->Rol;
        $Cod_estatus = $data->Cod_status;
        $Descripcion = $data->Descripcion;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_rol SET Rol=upper('$rol'),Cod_estatus=$Cod_estatus, Descripcion=upper('$Descripcion'),Usuario_modificacion='$Usuario_registro', Fecha_modificacion=now() WHERE Cod_rol='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_rol;
        $query = "DELETE FROM tbl_rol
        WHERE Cod_rol='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/objetos', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_objetos";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/objetos', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Nombre_objeto = $data->Nombre_objeto;
        $Descripcion_objeto = $data->Descripcion_objeto;
        $Tipo_objeto = $data->Tipo_objeto;
        $query = "INSERT INTO tbl_objetos
        (Nombre_objeto, Descripcion_objeto, Tipo_objeto)
        VALUES(upper('$Nombre_objeto'),upper('$Descripcion_objeto'),upper('$Tipo_objeto'))";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_objeto;
        $Nombre_objeto = $data->Nombre_objeto;
        $Descripcion_objeto = $data->Descripcion_objeto;
        $Tipo_objeto = $data->Tipo_objeto;
        $query = "UPDATE tbl_objetos SET Nombre_objeto=upper('$Nombre_objeto'), Descripcion_objeto=upper('$Descripcion_objeto'),Tipo_objeto=upper('$Tipo_objeto') WHERE Cod_objeto='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_objeto;
        $query = "DELETE FROM tbl_objetos
        WHERE Cod_objeto='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/preguntas', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_preguntas";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/preguntas', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $pregunta = $data->pregunta;
        $estatus = $data->estatus;
        $query = "INSERT INTO tbl_preguntas
        (pregunta, estatus)
        VALUES(upper('$pregunta'),'$estatus')";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->id_pregunta;
        $pregunta = $data->pregunta;
        $estatus = $data->estatus;
        $query = "UPDATE tbl_preguntas SET pregunta=upper('$pregunta'), estatus='$estatus' WHERE id_pregunta='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->id_pregunta;
        $query = "DELETE FROM tbl_preguntas
        WHERE id_pregunta='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/informe_academico', function () {
    require_once('./conn.php');
    $query = "select * from tbl_informe_academico i inner join tbl_matricula tm on i.Cod_matricula = tm.Cod_matricula inner join tbl_persona tp on i.Cod_persona = tp.Cod_persona ";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/informe_academico', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_matricula = $data->Cod_matricula;
        $Cod_persona = $data->Cod_persona;
        $Doc_informe_academico = $data->Doc_informe_academico;
        $Doc_planificacion_terapia = $data->Doc_planificacion_terapia;
        $Usuario_registro = $data->Usuario_registro;
        $query = "INSERT INTO tbl_informe_academico
        (Cod_matricula, Cod_persona, Doc_informe_academico, Doc_planificacion_terapia, Usuario_registro, Fecha_registro)
        VALUES($Cod_matricula,$Cod_persona,'$Doc_informe_academico','$Doc_planificacion_terapia','$Usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_informe_academico;
        $Doc_informe_academico = $data->Doc_informe_academico;
        $Doc_planificacion_terapia = $data->Doc_planificacion_terapia;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_informe_academico
        SET Doc_informe_academico='$Doc_informe_academico', Doc_planificacion_terapia='$Doc_planificacion_terapia',Usuario_modificacion='$Usuario_registro', Fecha_modificacion=now()
        WHERE Cod_informe_academico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_informe_academico;
        $query = "DELETE FROM tbl_informe_academico
        WHERE Cod_informe_academico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/estado_civil', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_estado_civil";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});



$app->get('/departamento', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_departamento";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->get('/tipoinstitucion', function () {
    require_once('./conn.php');
    $query = "SELECT *
    FROM tbl_tipo_institucion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->get('/bitacora', function () {
    require_once('./conn.php');
    include_once 'consultas.class.php';
    $instancia_db = new DbModelo();
    $query = "select * from tbl_bitacora tb inner join tbl_usuarios tu on tb.Cod_usuario = tu.id_usuario inner join tbl_objetos to2 on tb.Cod_objeto = to2.Cod_objeto";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/bitacora', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_usuario = $data->Cod_usuario;
        $Cod_objeto = $data->Cod_objeto;
        $Accion = $data->Accion;
        $Descripcion = $data->Descripcion;

        $query = "call bitacora($Cod_usuario,$Cod_objeto,'$Accion','$Descripcion')";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_informe_academico;
        $Cod_matricula = $data->Cod_matricula;
        $Cod_persona = $data->Cod_persona;
        $Doc_informe_academico = $data->Doc_informe_academico;
        $Doc_planificacion_terapia = $data->Doc_planificacion_terapia;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_informe_academico
        SET Cod_matricula=$Cod_matricula, Cod_persona=$Cod_persona, Doc_informe_academico='$Doc_informe_academico', Doc_planificacion_terapia='$Doc_planificacion_terapia',Usuario_modificacion='$Usuario_registro', Fecha_modificacion=now()
        WHERE Cod_informe_academico='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->id_pregunta;
        $query = "DELETE FROM tbl_preguntas
        WHERE id_pregunta='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/usuario', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_usuarios tu inner join tbl_rol tr on tu.Cod_rol = tr.Cod_rol inner join tbl_colaborador tc on tu.Cod_colaborador = tc.Cod_colaborador 
    inner join tbl_sede ts on tu.Cod_sede = ts.Cod_sede ORDER BY tu.id_usuario ASC";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/usuario', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    include_once 'consultas.class.php';
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;
    $instancia = new DbModelo();
    if ($tipo == "post") {
        $id_usuario = $data->id_usuario;
        $Cod_rol = $data->Cod_rol;
        $Cod_sede = $data->Cod_sede;
        $nombre_usuario = $data->nombre_usuario;
        $pass = $data->pass;
        $estado_usuario = $data->estado_usuario;
        $correo_electronico = $data->correo_electronico;
        $data_info = $instancia->getExisteUsuario(strtoupper($nombre_usuario));
        if ($data_info == true) {
            $respuesta = [
                'error' => true,
                'mensaje' => 'Ya existe un usuario con este nombre'
            ];
        } else {
            $query = "INSERT INTO tbl_usuarios
            (Cod_rol, Cod_sede, nombre_usuario, pass, estado_usuario,correo_electronico, estado_pass)
            VALUES($Cod_rol, $Cod_sede ,upper('$nombre_usuario'),AES_ENCRYPT('$pass','_sis_'),'$estado_usuario','$correo_electronico',1)";
            $result = mysqli_query($con, $query);
            $error = mysqli_error($con);
            $respuesta = [
                'error' => false,
                'mensaje' => $error
            ];
        }
    }

    if ($tipo == "update") {
        $id_usuario = $data->id_usuario;
        $Cod_rol = $data->Cod_rol;
        $Cod_sede = $data->Cod_sede;
        $nombre_usuario = $data->nombre_usuario;
        $nomUser = (strtoupper($nombre_usuario));
        $pass = $data->pass;
        $estado_usuario = $data->estado_usuario;
        $correo_electronico = $data->correo_electronico;
        $data_info = $instancia->getExisteUsuarioActualizar($id_usuario, $nomUser);
        if (count($data_info) >= 1 && $data_info['nombre_usuario'] <> $nomUser) {
            $respuesta = [
                'error' => true,
                'mensaje' => 'Ya existe un usuario con este nombre'
            ];
        } else {
            $query = "UPDATE tbl_usuarios
            SET Cod_rol=$Cod_rol,Cod_sede=$Cod_sede, nombre_usuario=upper('$nombre_usuario'), pass=AES_ENCRYPT('$pass','_sis_'), estado_usuario='$estado_usuario', estado_login='PV',correo_electronico='$correo_electronico' WHERE id_usuario=$id_usuario";
            $result = mysqli_query($con, $query);
            $error = mysqli_error($con);
            $respuesta = [
                'error' => false,
                'mensaje' => $error
            ];
        }
    }


    if ($tipo == "delete") {
        $cod = $data->id_pregunta;
        $query = "DELETE FROM tbl_preguntas
        WHERE id_pregunta='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});



$app->get('/servicio_social', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_servicio_social";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/servicio_social', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_estatus = $data->Cod_estatus;
        $Nombre_servicio_social = $data->Nombre_servicio_social;
        $Descripcion_servicio_social = $data->Descripcion_servicio_social;
        $Usuario_registro = $data->Usuario_registro;


        $query = "INSERT INTO tbl_servicio_social
        (Cod_estatus, Nombre_servicio_social, Descripcion_servicio_social, Usuario_registro, Fecha_registro)
        VALUES($Cod_estatus,upper('$Nombre_servicio_social'),upper('$Descripcion_servicio_social'),'$Usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_servicio_social;
        $Cod_estatus = $data->Cod_estatus;
        $Nombre_servicio_social = $data->Nombre_servicio_social;
        $Descripcion_servicio_social = $data->Descripcion_servicio_social;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_servicio_social
        SET Cod_estatus=$Cod_estatus, Nombre_servicio_social=upper('$Nombre_servicio_social'), Descripcion_servicio_social=upper('$Descripcion_servicio_social'),Usuario_modificacion='$Usuario_registro', Fecha_modificacion=now()
        WHERE Cod_servicio_social='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_servicio_social;
        $query = "DELETE FROM tbl_servicio_social
        WHERE Cod_servicio_social='$cod';
        ";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});

$app->get('/estado_nutricional', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_usuarios tu inner join tbl_rol tr on tu.Cod_rol = tr.Cod_rol inner join tbl_colaborador tc on tu.Cod_colaborador = tc.Cod_colaborador 
    inner join tbl_sede ts on tu.Cod_sede = ts.Cod_sede";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->get('/matric', function () {
    require_once('./conn.php');
    $query = "select * from tbl_matricula";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->get('/ficha_inclusion', function () {
    require_once('./conn.php');
    $query = "select * from tbl_ficha_inclusion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->get('/matricula', function () {
    require_once('./conn.php');
    $query = "select * from tbl_matricula tm inner join tbl_asignacion_terapeuta tat on tm.Cod_asignacion = tat.Cod_asignacion inner join tbl_persona tp 
    on tm.Cod_persona = tp.Cod_persona inner join tbl_ficha_inclusion tfi on tm.Cod_ficha_inclusion = tfi.Cod_ficha_inclusion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/matricula', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {

        $Cod_persona = $data->Cod_persona;
        $Cod_ficha_inclusion = $data->Cod_ficha_inclusion;
        $Horas_asignadas_beneficiario = $data->horas_asignadas_beneficiario;
        $Observacion_terapia = $data->Observacion_terapia;
        $Usuario_registro = $data->Usuario_registro;
        $Cod_colaborador = $data->Cod_colaborador;
        $Cod_tipo_especialidad = $data->Cod_tipo_especialidad;
        $horas_asignadas = $data->horas_asignadas;

        $query = "call matricula_asignacion('$Cod_colaborador','$Cod_tipo_especialidad','$horas_asignadas','$Usuario_registro','$Cod_persona','$Cod_ficha_inclusion','$Horas_asignadas_beneficiario','$Observacion_terapia')";

        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_asignacion;
        $Cod_persona = $data->Cod_persona;
        $Cod_ficha_inclusion = $data->Cod_ficha_inclusion;
        $Horas_asignadas_beneficiario = $data->horas_asignadas_beneficiario;
        $Observacion_terapia = $data->Observacion_terapia;
        $Usuario_registro = $data->Usuario_registro;
        $Cod_colaborador = $data->Cod_colaborador;
        $Cod_tipo_especialidad = $data->Cod_tipo_especialidad;
        $horas_asignadas = $data->horas_asignadas;
        $query = "call maricula_asignacion_edit('$cod','$Cod_colaborador','$Cod_tipo_especialidad','$horas_asignadas','$Usuario_registro','$Cod_persona','$Cod_ficha_inclusion','$Horas_asignadas_beneficiario','$Observacion_terapia')";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_servicio_social;
        $query = "DELETE FROM tbl_servicio_social
        WHERE Cod_servicio_social='$cod';
        ";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/asignacion_terapeutica', function () {
    require_once('./conn.php');
    $query = "SELECT * from tbl_asignacion_terapeuta";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->post('/asignacion_terapeutica', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_asignacion = $data->Cod_asignacion;
        $Cod_persona = $data->Cod_persona;
        $Cod_ficha_inclusion = $data->Cod_ficha_inclusion;
        $Horas_asignadas_beneficiario = $data->Horas_asignadas_beneficiario;
        $Observacion_terapia = $data->Observacion_terapia;
        $Usuario_registro = $data->Usuario_registro;


        $query = "INSERT INTO tbl_matricula
        (Cod_asignacion, Cod_persona, Cod_ficha_inclusion, Horas_asignadas_beneficiario, Observacion_terapia, Usuario_registro, Fecha_registro, Usuario_modificacion, Fecha_modificacion)
        VALUES($Cod_asignacion,$Cod_persona, $Cod_ficha_inclusion,$Horas_asignadas_beneficiario,'$Observacion_terapia','$Usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'creado correctamente'
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_matricula;
        $Cod_asignacion = $data->Cod_asignacion;
        $Cod_persona = $data->Cod_persona;
        $Cod_ficha_inclusion = $data->Cod_ficha_inclusion;
        $Horas_asignadas_beneficiario = $data->Horas_asignadas_beneficiario;
        $Observacion_terapia = $data->Observacion_terapia;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_matricula
        SET Cod_asignacion=$Cod_asignacion, Cod_persona=$Cod_persona, Cod_ficha_inclusion=$Cod_ficha_inclusion, Horas_asignadas_beneficiario=$Horas_asignadas_beneficiario,
         Observacion_terapia='$Observacion_terapia',Usuario_modificacion='$Usuario_registro', Fecha_modificacion=now() WHERE Cod_matricula=$cod";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Editado correctamente'
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_servicio_social;
        $query = "DELETE FROM tbl_servicio_social
        WHERE Cod_servicio_social='$cod';
        ";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/registro_social', function () {
    require_once('./conn.php');
    $query = "select * from tbl_registro_servicio_social r inner join tbl_colaborador tc on 
    r.Cod_colaborador = tc.Cod_colaborador inner join tbl_servicio_social tss on
    r.Cod_servicio_social = tss.Cod_servicio_social inner join tbl_persona tp on r.Cod_persona = tp.Cod_persona";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/registro_social', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_colaborador = $data->Cod_colaborador;
        $Cod_servicio_social = $data->Cod_servicio_social;
        $Fecha_realizacion = $data->Fecha_realizacion;
        $Usuario_registro = $data->Usuario_registro;
        $Cod_persona = $data->Cod_persona;
        $query = "INSERT INTO tbl_registro_servicio_social
        (Cod_colaborador, Cod_servicio_social, Fecha_realizacion, Usuario_registro, Fecha_registro,Cod_persona)
        VALUES($Cod_colaborador,$Cod_servicio_social,'$Fecha_realizacion','$Usuario_registro',now(),$Cod_persona)";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_registro_servicio_social;
        $Cod_servicio_social = $data->Cod_servicio_social;
        $Fecha_realizacion = $data->Fecha_realizacion;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_registro_servicio_social
        SET  Cod_servicio_social=$Cod_servicio_social, Fecha_realizacion='$Fecha_realizacion',
        Usuario_modificacion='$Usuario_registro',Fecha_modificacion=now()
        WHERE Cod_registro_servicio_social=$cod";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_registro_servicio_social;
        $query = "DELETE FROM tbl_registro_servicio_social
        WHERE Cod_registro_servicio_social='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});



$app->get('/solicitud_evaluacion', function () {
    require_once('./conn.php');
    $query = "select * from tbl_solicitud_evaluacion";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});

$app->post('/solicitud_evaluacion', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $tipo = $data->tipo;

    if ($tipo == "post") {
        $Cod_departamento = $data->Cod_departamento;
        $Cod_sede = $data->Cod_sede;
        $Cod_tipo_evaluacion = $data->Cod_tipo_evaluacion;
        $Cod_parentesco = $data->Cod_parentesco;
        $Cod_estatus = $data->Cod_estatus;
        $Nombre_beneficiario = $data->Nombre_beneficiario;
        $Edad_beneficiario = $data->Edad_beneficiario;
        $Direccion_actual = $data->Direccion_actual;
        $Nombre_responsable = $data->Nombre_responsable;
        $Telefono_fijo = $data->Telefono_fijo;
        $Telefono_celular = $data->Telefono_celular;
        $Correo_electronico = $data->Correo_electronico;
        $Fecha_solicitud = $data->Fecha_solicitud;
        $Usuario_registro = $data->Usuario_registro;



        $query = "INSERT INTO tbl_solicitud_evaluacion
        (Cod_departamento,Cod_sede, Cod_tipo_evaluacion, Cod_parentesco, Cod_estatus, Nombre_beneficiario,
         Edad_beneficiario, Direccion_actual, Nombre_responsable, Telefono_fijo, Telefono_celular, Correo_electronico,
          Fecha_solicitud, Usuario_registro, Fecha_registro)
        VALUES($Cod_departamento,$Cod_sede,$Cod_tipo_evaluacion,$Cod_parentesco,$Cod_estatus,'$Nombre_beneficiario','$Edad_beneficiario','$Direccion_actual',
                '$Nombre_responsable',$Telefono_fijo,$Telefono_celular,'$Correo_electronico','$Fecha_solicitud','$Usuario_registro',now())";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "update") {
        $cod = $data->Cod_registro_servicio_social;
        $Cod_colaborador = $data->Cod_colaborador;
        $Cod_servicio_social = $data->Cod_servicio_social;
        $Documento_listado_participante = $data->Documento_listado_participante;
        $Fecha_realizacion = $data->Fecha_realizacion;
        $Usuario_registro = $data->Usuario_registro;
        $query = "UPDATE tbl_registro_servicio_social
        SET Cod_colaborador=$Cod_colaborador, Cod_servicio_social=$Cod_servicio_social, Documento_listado_participante='$Documento_listado_participante', Fecha_realizacion='$Fecha_realizacion',
        Usuario_modificacion='$Usuario_registro',Fecha_modificacion=now()
        WHERE Cod_registro_servicio_social=$cod";
        $result = mysqli_query($con, $query);
        $error = mysqli_error($con);
        $respuesta = [
            'error' => false,
            'mensaje' => $error
        ];
    }


    if ($tipo == "delete") {
        $cod = $data->Cod_registro_servicio_social;
        $query = "DELETE FROM tbl_registro_servicio_social
        WHERE Cod_registro_servicio_social='$cod'";
        $result = mysqli_query($con, $query);
        $respuesta = [
            'error' => false,
            'mensaje' => 'Eliminado correctamente'
        ];
    }

    echo json_encode($respuesta);
});


$app->get('/permiso_mostrar/:user', function ($user) {
    require_once('./conn.php');
    $query = '';
    //$query = "select * from tbl_permisos t where t.Cod_rol = 3";
    $query = "SELECT o.Nombre_objeto,p.Permiso_insertar,p.Permiso_eliminar,p.Permiso_actualizar, p.Permiso_consultar, r.Rol, e.Estatus
        FROM tbl_usuarios u 
        INNER JOIN tbl_rol r on u.Cod_rol=r.Cod_rol
        INNER JOIN tbl_permisos p on r.Cod_rol=p.Cod_rol
        INNER JOIN tbl_objetos o on p.Cod_objeto=o.Cod_objeto
        INNER JOIN tbl_estatus e on r.Cod_estatus= e.Cod_estatus
        WHERE u.nombre_usuario='$user'";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});


$app->get('/permisos_data/:id', function ($id) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');

    $query = "SELECT * from tbl_objetos t inner join tbl_permisos tp on t.Cod_objeto = tp.Cod_objeto where tp.Cod_rol = 1 and t.Cod_objeto = '$id'";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $resp[] = $row;
    }

    echo json_encode($resp);
});




$app->post('/filtro', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $id = $data->filtro;
    $query = "select * from tbl_persona tp where tp.No_identidad = '$id'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $resp[] = $row;
        }
    } else {
        $resp = [
            'error' => false,
            'mensaje' => 'sin resultado'
        ];
    }
    echo json_encode($resp);
});


$app->post('/filtrocolaborador', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $id = $data->filtro;
    $query = "select tp.Nombre, tp.Apellido, tc.Cod_colaborador  from tbl_persona tp inner join tbl_colaborador tc on tp.Cod_persona = tc.Cod_persona where tp.No_identidad = '$id'";
    $result = mysqli_query($con, $query);


    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $resp[] = $row;
        }
    } else {
        $resp = [
            'error' => false,
            'mensaje' => 'sin resultado'
        ];
    }


    echo json_encode($resp);
});


$app->post('/matriculaid', function () use ($app) {
    $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
    require_once('./conn.php');
    $json = $app->request->getBody();
    $data = json_decode($json);
    $id = $data->persona;
    $query = "select tm.Fecha_registro, tp.Cod_persona,tm.Cod_matricula  from tbl_matricula tm inner join tbl_persona tp on tm.Cod_persona = tp.Cod_persona where tp.Cod_persona  = '$id'";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $resp[] = $row;
    }

    echo json_encode($resp);
});


$app->get('/pers', function () {
    require_once('./conn.php');
    $query = "select * from tbl_persona";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
});



$app->get('/prueba', function () {
    require_once('./conn.php');
    $query = "select * from tbl_persona";
    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
});



$app->run();
