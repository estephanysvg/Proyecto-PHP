<?php

require_once 'vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();

$id = $cli->id;
$first_name = $cli->first_name;
$last_name = $cli->last_name;
$email = $cli->email;
$gender = $cli->gender;
$ip_address = $cli->ip_address;
$telefono = $cli->telefono;

$ruta = ponerFoto($id);
// Especifica la ruta completa de la imagen
$rutaImagen = $ruta;

// Estilo CSS incorporado
$css = '
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #3498db; /* Azul claro */
        }
        table {
            border-collapse: collapse;
            width: 100%;
            border: 2px solid #3498db; /* Azul claro */
        }
        th, td {
            border: 1px solid #3498db; /* Azul claro */
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #3498db; /* Azul claro */
            color: #fff; /* Blanco */
        }
        img {
            border: 2px solid #3498db; /* Azul claro */
            margin-bottom: 10px;
        }
    </style>
';

$mpdf->WriteHTML($css);

$mpdf->WriteHTML('<h1>CLIENTE</h1>');
$mpdf->Image($rutaImagen, $x = 75, $y = 20, $w = 60, $h = 60);
$mpdf->WriteHTML('<br><br><table>
        <tr>
            <th>ID</th>
            <th>First_nombre</th>
            <th>Last_name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>IP_address</th>
            <th>Teléfono</th>
        </tr>
        <tr>
            <td>' . $id . '</td>
            <td>' . $first_name . '</td>
            <td>' . $last_name . '</td>
            <td>' . $email . '</td>
            <td>' . $gender . '</td>
            <td>' . $ip_address . '</td>
            <td>' . $telefono . '</td>
        </tr>
    </table>');

$mpdf->Output($first_name . '.pdf', 'D');
