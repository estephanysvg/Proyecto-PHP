<?php
session_start();
define('FPAG', 10); // Número de filas por página


require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';
$msg ="";

//---- PAGINACIÓN ----
$midb = AccesoDatos::getModelo();
$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
    $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------

//---- PROCESO DE ORDENES ----

//Sesion para ordenar por campo, ordena por id por defecto
if (!isset($_SESSION['ordenCampo'])) {
    $_SESSION['ordenCampo'] = "id";
}
//Sesion para ordenar de manera ascendente o descendente, si pulsamos varias veces en el mismo campo, se cambia el orden de manera ascendente a descendente y viceversa
if (!isset($_SESSION['ordenAscDesc'])) {
    $_SESSION['ordenAscDesc'] = "";
}
//Sesion para entrar en la pagina
if (!isset($_SESSION['entrar'])) {
    $_SESSION['entrar'] = false;
}
//Seseion para los intentos de login
if (!isset($_SESSION['intentos'])) {
    $_SESSION['intentos'] = 0;
}
//Sesion para el rol
if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = "";
}

if ($_SESSION['entrar'] == true && $_SESSION['intentos'] < 3) {
    ob_start(); // La salida se guarda en el bufer
    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        // Proceso las ordenes de navegación
        if (isset($_GET['nav'])) {
            switch ($_GET['nav']) {
                case "Primero":
                    $posAux = 0;
                    break;
                case "Siguiente":
                    $posAux += FPAG;
                    if ($posAux > $posfin) $posAux = $posfin;
                    break;
                case "Anterior":
                    $posAux -= FPAG;
                    if ($posAux < 0) $posAux = 0;
                    break;
                case "Ultimo":
                    $posAux = $posfin;
            }
            $_SESSION['posini'] = $posAux;
        }


        // Proceso las ordenes de navegación en detalles
        if (isset($_GET['nav-detalles']) && isset($_GET['id'])) {
            switch ($_GET['nav-detalles']) {
                case "Siguiente":
                    crudDetallesSiguiente($_GET['id']);
                    break;
                case "Anterior":
                    crudDetallesAnterior($_GET['id']);
                    break;
                case "Imprimir":
                    crudDetallesImprimir($_GET['id']);
                    break;
            }
        }

        // Proceso las ordenes de navegación en modificación
        if (isset($_GET['nav-modificar']) && isset($_GET['id'])) {
            switch ($_GET['nav-modificar']) {
                case "Siguiente":
                    crudModificarSiguiente($_GET['id']);
                    break;
                case "Anterior":
                    crudModificarAnterior($_GET['id']);
                    break;
            }
        }

        // Proceso de ordenes de CRUD clientes
        if (isset($_GET['orden'])) {
            switch ($_GET['orden']) {
                case "Nuevo":
                    crudAlta();
                    break;
                case "Borrar":
                    crudBorrar($_GET['id']);
                    break;
                case "Modificar":
                    crudModificar($_GET['id']);
                    break;
                case "Detalles":
                    crudDetalles($_GET['id']);
                    break;
                case "Terminar":
                    crudTerminar();
                    break;
                case "Ordenar":
                    $_SESSION['ordenCampo'] = $_GET['valor'];
                    if ($_SESSION['ordenAscDesc'] == "ASC") {
                        $_SESSION['ordenAscDesc'] = "DESC";
                    } else {
                        $_SESSION['ordenAscDesc'] = "ASC";
                    }
                    break;
            }
        }
    }
    // POST Formulario de alta o de modificación
    else {
        if (isset($_POST['orden'])) {
            switch ($_POST['orden']) {
                case "Nuevo":
                    crudPostAlta();
                    break;
                case "Modificar":
                    crudPostModificar();
                    break;
                case "Detalles":; // No hago nada
            }
        }
    }


    if (ob_get_length() == 0) {
        $db = AccesoDatos::getModelo();
        $posini = $_SESSION['posini'];
        $campoAordenar = $_SESSION['ordenCampo'];
        $metodoOrdenacion = $_SESSION['ordenAscDesc'];
        $tvalores = $db->getClientes($posini, FPAG, $campoAordenar, $metodoOrdenacion);
        if ($_SESSION['rol'] == 1) {
            include_once "app/views/list.php";
        } else {
            include_once "app/views/listaNoAcceso.php";
        }

        // Muestro la página principal
    }
} else {
    include_once "app/views/login.php";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (comprobarUsuario($_POST['user'], $_POST['passwd'])) {
            $_SESSION['entrar'] = true;
            $_SESSION['rol'] = comprobarRol($_POST['user']);
            header("Location: index.php");
        } else {
            $msg = "<h4 style='color: blue;'>Incorrecto</h4>";
            $_SESSION['intentos']++;
        }
    }
    if ($_SESSION['intentos'] >= 3) {
        $msg ="";
        echo "<h4 style='color: red;'>Has superado el número máximo de intentos, cierra el navegador y prueba de nuevo</h4>";
    }
}

$contenido = ob_get_clean();
include_once "app/views/principal.php";
