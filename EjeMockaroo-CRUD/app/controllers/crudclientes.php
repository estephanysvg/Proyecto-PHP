<?php
 
function crudBorrar ($id){    
    $msg = "";
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ( $resu){
         $msg .= " El usuario ".$id. " ha sido eliminado.";
    } else {
         $msg.= " Error al eliminar el usuario ".$id.".";
    }

}

function crudTerminar(){
    $msg = "";

    AccesoDatos::closeModelo();
    session_destroy();
}
 
function crudAlta(){
    $msg = "";
    $db = AccesoDatos::getModelo();
    $cli = new Cliente();
    $orden= "Nuevo";
    $cli->id = $db->getUltimoId();
    include_once "app/views/formulario.php";
}

function crudDetalles($id){
    $msg = "";

    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    include_once "app/views/detalles.php";
}


function crudDetallesSiguiente($id)
{
    $msg = "";

    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    if (!$cli) {
        crudDetalles($id);
    } else {
        include_once "app/views/detalles.php";
    }
}
function crudDetallesAnterior($id)
{
    $msg = "";
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    if (!$cli) {
        crudDetalles($id);

    } else {
       include_once "app/views/detalles.php";

    }
}

function crudModificar($id){
    $msg = "";
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden="Modificar";
    include_once "app/views/formulario.php";
}



function mostrarBandera($ip)
{
    $msg = "";


    $urlApi = "http://ip-api.com/json/" . $ip;
    $jsonUrl = file_get_contents($urlApi);
    $datos = json_decode($jsonUrl, true);
    if(!($datos["status"] == "fail")){
    $pais = $datos['country'];
    $bandera = $datos['countryCode'];
    $bandera = strtolower($bandera);
    $bandera = "https://flagpedia.net/data/flags/w580/$bandera.webp";
    echo "<img src='$bandera'";
    }else{
        //no bandera
    }
    
}

function mostrarMapa($ip)
{
    $msg = "";

    $url = "http://ip-api.com/json/" . $ip;
    $json = file_get_contents($url);
    $datos = json_decode($json, true);

    if(!($datos["status"] == "fail")){
        $latitud = $datos['lat'];
        $longitud = $datos['lon'];
        echo "<iframe src='https://maps.google.com/maps?q=$latitud,$longitud&z=15&output=embed' width='200' height='200' ></iframe>";
    }else{
        //no mapa
    }

}

function mostrarFoto($id) 
{
    $msg = "";

    define("DIRECTORIO", 'app\uploads\\');

    $ceros = 0;
    $fichero = str_pad($ceros, 7, "0", STR_PAD_LEFT);
    $nombreFichero = substr($fichero, 0, 8 - strlen($id)) . $id;
    $fichero = "app/uploads/{$nombreFichero}.jpg";

    if (file_exists($fichero)) {
        echo "<img src='{$fichero}' alt='Foto cliente'>";
    } else {
        echo "<img src='https://robohash.org/{$id}' width='20' alt='Foto perfil robot'>";
    }
}

function ponerFoto($id) 
{
    $msg = "";

    define("DIRECTORIO", 'app\uploads\\');

    $ceros = 0;
    $fichero = str_pad($ceros, 7, "0", STR_PAD_LEFT);
    $nombreFichero = substr($fichero, 0, 8 - strlen($id)) . $id;
    $fichero = "app/uploads/{$nombreFichero}.jpg";

    if (file_exists($fichero)) {
        $ruta = $fichero;
    } else {
        $ruta = "https://robohash.org/{$id}";
    }

    return $ruta;
}


function crudModificarSiguiente($id)
{
    $msg = "";

    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    if (!$cli) {
        crudModificar($id);
    } else {
        $orden = "Modificar";
        include_once "app/views/formulario.php";
    }
}

function crudModificarAnterior($id)
{
    $msg = "";

    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    if (!$cli) {
        crudModificar($id);

    } else {
        $orden = "Modificar";
        include_once "app/views/formulario.php";
    }
}

function crudPostAlta()
{
    $msg = "";
 
    $db = AccesoDatos::getModelo();
    limpiarArrayEntrada($_POST); 
    $cli = new Cliente();
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

    $correcto = true;

    if (emailRepetido($cli->email)) {
        $msg .= "Email repetido";
        $cli->email = "";
        $correcto = false;
    }

    if (!ipCorrecta($cli->ip_address)) {
        $msg .= "El IP no es correcto.";
        $cli->ip_address = "";
        $correcto = false;
    }
    if (!telefonoCorrecto($cli->telefono)) {
        $msg .= "El teléfono no tiene el fromato correcto.";
        $cli->telefono = "";
        $correcto = false;
        
    }

    if ($correcto) {
        $db->addCliente($cli);
        $idCliente = $db->getIdCliente($cli->email);
        subirFoto($idCliente);
        $msg .= " El usuario ".$cli->first_name." se ha dado de alta ";
    } else {
        $msg.= " Error al dar de alta al usuario ".$cli->first_name."."; 
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
    }
}

function crudPostModificar()
{
    $msg = "";
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();

    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];
    $db = AccesoDatos::getModelo();


    $correcto = true;

    if (emailRepetidoModificar($cli->email, $cli->id)) {
        $msg.= "Email repetido";
        $cli->email = "";
        $correcto = false;
    }

    if (!ipCorrecta($cli->ip_address)) {
        $msg.= "El IP no es correcto.";
        $cli->ip_address = "";
        $correcto = false;
    }
    if (!telefonoCorrecto($cli->telefono)) {
        $msg= "El teléfono no tiene el fromato correcto.";
        $cli->telefono = "";
        $correcto = false;
    }
    if ($_POST['first_name'] == "") {
        $msg .= "Nombre vacio";

        $cli->first_name = "";
        $correcto = false;
    }
    if ($_POST['last_name'] == "") {
        $msg .= "Apellido vacio";
        $cli->last_name = "";
        $correcto = false;
    }

    if ( $correcto ){
        $db->modCliente($cli);
        subirFoto($cli->id);
        $msg .= " El usuario ha sido modificado";
    } else {
        $msg .= " Error al modificar el usuario ";
        $orden = "Modificar";
        include_once "app/views/formulario.php";
    }
}


function ipCorrecta($ip)
{
    $formato = true;
    $patron = "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/";

    if (preg_match($patron, $ip)) {
        $formato = true;
    } else {
        $formato = false;
    }

    return $formato;
}


function emailRepetido($email)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteEmail($email);
    if ($cli) {
        return true;
    } else {
        return false;
    }
}


function emailRepetidoModificar($email, $id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteEmail($email);
    if ($cli) {
        if ($cli->id == $id) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}


function telefonoCorrecto($telefono)
{
    $formato = true;
    $patron = "/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/";

    if (preg_match($patron, $telefono)) {
        $formato = true;
    } else {
        $formato = false;
    }

    return $formato;
}


function comprobarUsuario($usuario, $contrasena)
{
    $db = AccesoDatos::getModelo();
    $usu = $db->getUser($usuario, $contrasena);

    if ($usu) {
        return true;
    } else {
        return false;
    }
}

function comprobarRol($usuario)
{
    $db = AccesoDatos::getModelo();
    $usu = $db->getRol($usuario);
    if ($usu) {
        return $usu;
    } else {
        return false;
    }
}

function subirFoto($id)
{
    $msg = "";
    $ceros = 0;
    $fichero = str_pad($ceros, 7, "0", STR_PAD_LEFT);
    $fichero = substr($fichero, 0, 8 - strlen($id)) . $id;
    $ruta = 'app/uploads/' . $fichero . '.jpg';
    

    $nombre = $_FILES['foto']['name'];
    $tipo = $_FILES['foto']['type'];
    $tamano = $_FILES['foto']['size'];

    if ($nombre != "") {
        if ($tipo == "image/jpeg" || $tipo == "image/jpg") {
            if ($tamano <= 500000) {
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
            } else {
               $msg .= "El tamaño es demasiado grande";
            }
        } else {
           $msg .= "El formato no es el correcto";
        }
    }
}

function crudDetallesImprimir($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    include_once "app/views/generaPDFs.php";
}